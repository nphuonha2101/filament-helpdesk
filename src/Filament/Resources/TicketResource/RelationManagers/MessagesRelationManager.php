<?php

namespace Nphuonha\FilamentHelpdesk\Filament\Resources\TicketResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Nphuonha\FilamentHelpdesk\Notifications\TicketReplyNotification;

class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('body')
                    ->required()
                    ->maxLength(65535)
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
                Tables\Actions\CreateAction::make()
                    ->label('Reply')
                    ->after(function ($record) {
                        // Send notification to the ticket owner
                        $ticket = $record->ticket;
                        if ($ticket->email) {
                            Notification::route('mail', $ticket->email)
                                ->notify(new TicketReplyNotification($ticket, $record));
                        } elseif ($ticket->user) {
                            $ticket->user->notify(new TicketReplyNotification($ticket, $record));
                        }
                    }),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }
}
