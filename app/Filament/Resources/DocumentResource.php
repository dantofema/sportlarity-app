<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Models\Document;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->columnSpanFull()
                    ->required()
                    ->maxLength(191),
                Forms\Components\FileUpload::make('image')
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
                            \Illuminate\Support\Str::random(8),
                            $file->getClientOriginalExtension()
                        )
                    ),
                Forms\Components\FileUpload::make('file')
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
                            \Illuminate\Support\Str::random(8),
                            $file->getClientOriginalExtension()
                        )
                    )
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\IconColumn::make('file')
                    ->label('')
                    ->icon('heroicon-o-arrow-down-on-square')
                    ->url(fn (Document $document) => route('secure.document', $document->id)),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                ImageColumn::make('image')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder.png'))
                    ->url(fn (Document $document) => $document->image ? route('secure.document.image', $document->id) : null),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Created by')
                    ->numeric()
                    ->sortable(),

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'view' => Pages\ViewDocument::route('/{record}'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}
