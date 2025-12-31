<?php

namespace Nphuonha\FilamentHelpdesk\Filament\Resources;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;
use Nphuonha\FilamentHelpdesk\Filament\Resources\TicketResource\Pages;
use Nphuonha\FilamentHelpdesk\Models\Ticket;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-ticket';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Helpdesk';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('status')
                            ->options(\Nphuonha\FilamentHelpdesk\Enums\TicketStatus::class)
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('priority')
                            ->options(\Nphuonha\FilamentHelpdesk\Enums\TicketPriority::class)
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('assigned_to_user_id')
                            ->relationship('assignedTo', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('priority')
                    ->badge(),
                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Assigned To')
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('assigned_to_user_id')
                    ->relationship('assignedTo', 'name')
                    ->label('Assigned To')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('my_tickets')
                    ->label('My Tickets')
                    ->query(fn ($query) => $query->where('assigned_to_user_id', auth()->id())),
            ])
            ->actions([
<<<<<<< HEAD
                Actions\EditAction::make(),
=======
                Tables\Actions\Action::make('assign_to_me')
                    ->label('Assign to Me')
                    ->icon('heroicon-o-user')
                    ->action(fn (Ticket $record) => $record->update(['assigned_to_user_id' => auth()->id()]))
                    ->visible(fn (Ticket $record) => $record->assigned_to_user_id !== auth()->id()),
                Tables\Actions\EditAction::make(),
>>>>>>> feature/assign-ticket-supporter
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TicketResource\RelationManagers\MessagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}
