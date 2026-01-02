<?php

namespace Nphuonha\FilamentHelpdesk\Filament\Resources;

use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Nphuonha\FilamentHelpdesk\Filament\Resources\EmailTemplateResource\Pages;
use Nphuonha\FilamentHelpdesk\Models\EmailTemplate;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-document-text';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Helpdesk';
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\Section::make('Template Details')
                            ->columnSpan(1)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('subject_template')
                                    ->required()
                                    ->maxLength(255)
                                    ->live()
                                    ->helperText('Use {ticket_id}, {subject}, {status} as placeholders.'),
                                Forms\Components\RichEditor::make('body_template')
                                    ->required()
                                    ->live()
                                    ->helperText('Use {ticket_id}, {subject}, {status}, {body} as placeholders.')
                                    ->disableToolbarButtons(['attachFiles'])
                                    ->fileAttachmentsDisk(null)
                                    ->fileAttachmentsDirectory(null),
                            ]),
                        Forms\Components\Section::make('Live Preview')
                            ->columnSpan(1)
                            ->schema([
                                Forms\Components\Placeholder::make('preview_subject')
                                    ->content(fn ($get) => $get('subject_template') 
                                        ? str_replace(
                                            ['{ticket_id}', '{subject}', '{status}', '{body}'],
                                            ['#12345', 'Sample Ticket', 'Open', 'This is a sample ticket body.'],
                                            $get('subject_template')
                                        ) 
                                        : new \Illuminate\Support\HtmlString('<span class="text-gray-400 italic">Start typing to see preview...</span>')
                                    ),
                                Forms\Components\Placeholder::make('preview_body')
                                    ->content(fn ($get) => new \Illuminate\Support\HtmlString($get('body_template') 
                                        ? str_replace(
                                            ['{ticket_id}', '{subject}', '{status}', '{body}'],
                                            ['#12345', 'Sample Ticket', 'Open', 'This is a sample ticket body.'],
                                            $get('body_template')
                                        ) 
                                        : '<span class="text-gray-400 italic">Start typing to see preview...</span>'
                                    )),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject_template')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'edit' => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
