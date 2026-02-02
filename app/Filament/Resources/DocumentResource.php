<?php

namespace App\Filament\Resources;

use BackedEnum;
use App\Filament\Resources\DocumentResource\Pages\CreateDocument;
use App\Filament\Resources\DocumentResource\Pages\EditDocument;
use App\Filament\Resources\DocumentResource\Pages\ListDocuments;
use App\Filament\Resources\DocumentResource\Pages\ViewDocument;
use App\Models\Document;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-duplicate';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->columnSpanFull()
                    ->required()
                    ->maxLength(191),
                FileUpload::make('image')
                    ->disk('private_documents')
                    ->image()
                    ->maxSize(2048)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->rules([
                        'image',
                        'mimetypes:image/jpeg,image/png,image/webp',
                        'max:2048',
                        new ValidFileContent(['image/jpeg', 'image/png', 'image/webp']),
                    ])
                    ->getUploadedFileNameForStorageUsing(
                        fn ($file): string => sprintf(
                            'img-%s-%s.%s',
                            now()->format('Y-m-d-His'),
                            Str::random(8),
                            $file->getClientOriginalExtension()
                        )
                    ),
                FileUpload::make('file')
                    ->disk('private_documents')
                    ->maxSize(5120)
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-powerpoint',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                        'text/plain',
                        'text/csv',
                    ])
                    ->rules([
                        'file',
                        'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,text/plain,text/csv',
                        'max:5120',
                        new ValidFileContent([
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-powerpoint',
                            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                            'text/plain',
                            'text/csv',
                        ]),
                    ])
                    ->helperText('Permitido: PDF, Word, Excel, PowerPoint, TXT, CSV. Máximo 5MB.')
                    ->getUploadedFileNameForStorageUsing(
                        fn ($file): string => sprintf(
                            'doc-%s-%s.%s',
                            now()->format('Y-m-d-His'),
                            Str::random(8),
                            $file->getClientOriginalExtension()
                        )
                    )
                    ->required(),
                Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                IconColumn::make('file')
                    ->label('')
                    ->icon('heroicon-o-arrow-down-on-square')
                    ->url(fn (Document $document): string => route('secure.document', $document->id)),
                TextColumn::make('title')
                    ->searchable(),
                ImageColumn::make('image')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder.png'))
                    ->url(fn (Document $document): ?string => $document->image ? route('secure.document.image', $document->id) : null),
                TextColumn::make('user.name')
                    ->label('Created by')
                    ->numeric()
                    ->sortable(),

            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
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
            'index' => ListDocuments::route('/'),
            'create' => CreateDocument::route('/create'),
            'view' => ViewDocument::route('/{record}'),
            'edit' => EditDocument::route('/{record}/edit'),
        ];
    }
}
