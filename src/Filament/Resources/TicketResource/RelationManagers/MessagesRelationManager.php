<?php

namespace Nphuonha\FilamentHelpdesk\Filament\Resources\TicketResource\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Set;
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
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set, $livewire) {
                        $template = EmailTemplate::find($state);
                        if ($template) {
                            $ticket = $livewire->getOwnerRecord();
                            $body = str_replace(
                                ['{ticket_id}', '{subject}', '{status}'],
                                [$ticket->id, $ticket->subject, $ticket->status->getLabel() ?? (string) $ticket->status],
                                $template->body_template
                            );
                            $set('body', $body);
                        }
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
                Tables\Columns\IconColumn::make('email_sent')
                    ->label('Email Status')
                    ->boolean()
                    ->visible(fn ($record) => $record->is_admin_reply)
                    ->tooltip(fn ($record) => $record->email_error ?? ($record->email_sent ? 'Sent at ' . $record->email_sent_at?->format('Y-m-d H:i:s') : 'Not sent')),
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
                        
                        // Notification is queued, listener will handle success/failure
                        if ($ticket->email) {
                            Notification::route('mail', $ticket->email)
                                ->notify(new TicketReplyNotification($ticket, $record, $template));
                        } elseif ($ticket->user) {
                            $ticket->user->notify(new TicketReplyNotification($ticket, $record, $template));
                        }
                    }),
            ])
            ->actions([
                Actions\Action::make('retry_email')
                    ->label('Retry Send')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn ($record) => $record->is_admin_reply && !$record->email_sent && $record->email_error)
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $ticket = $record->ticket;
                        $templateId = $record->template_id ?? null;
                        $template = $templateId ? EmailTemplate::find($templateId) : null;
                        
                        try {
                            if ($ticket->email) {
                                Notification::route('mail', $ticket->email)
                                    ->notify(new TicketReplyNotification($ticket, $record, $template));
                            } elseif ($ticket->user) {
                                $ticket->user->notify(new TicketReplyNotification($ticket, $record, $template));
                            }
                            
                            $record->update([
                                'email_sent' => true,
                                'email_sent_at' => now(),
                                'email_error' => null,
                            ]);
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Email sent successfully')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            $record->update([
                                'email_sent' => false,
                                'email_error' => $e->getMessage(),
                            ]);
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Failed to send email')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                // Actions\EditAction::make(),
                // Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }
}
