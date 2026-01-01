<?php

namespace Nphuonha\FilamentHelpdesk\Filament\Resources\TicketResource\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Nphuonha\FilamentHelpdesk\Notifications\TicketReplyNotification;
use Nphuonha\FilamentHelpdesk\Models\EmailTemplate;

class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('template_id')
                    ->label('Email Template')
                    ->options(EmailTemplate::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->placeholder('Select a template (optional)')
                    ->columnSpanFull()
                    ->reactive() // or live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        // Optional: if you want to pre-fill the body based on template
                        // $template = EmailTemplate::find($state);
                        // if ($template) {
                        //     $set('body', $template->body_template);
                        // }
                    }),
                Forms\Components\RichEditor::make('body')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('attachments')
                    ->multiple()
                    ->columnSpanFull(),
                Forms\Components\Hidden::make('user_id')
                    ->default(Auth::id()),
                Forms\Components\Hidden::make('is_admin_reply')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('body')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->description(fn ($record) => $record->created_at->diffForHumans()),
                Tables\Columns\TextColumn::make('body')
                    ->html()
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('Reply')
                    ->after(function ($record, array $data) {
                        // Send notification to the ticket owner
                        $ticket = $record->ticket;
                        
                        $templateId = $data['template_id'] ?? null;
                        $template = $templateId ? EmailTemplate::find($templateId) : null;

                        if ($ticket->email) {
                            Notification::route('mail', $ticket->email)
                                ->notify(new TicketReplyNotification($ticket, $record, $template));
                        } elseif ($ticket->user) {
                            $ticket->user->notify(new TicketReplyNotification($ticket, $record, $template));
                        }
                    }),
            ])
            ->actions([
                // Actions\EditAction::make(),
                // Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }
}
