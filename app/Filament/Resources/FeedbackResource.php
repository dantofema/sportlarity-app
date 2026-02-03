<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeedbackResource\Pages\CreateFeedback;
use App\Filament\Resources\FeedbackResource\Pages\EditFeedback;
use App\Filament\Resources\FeedbackResource\Pages\ListFeedbacks;
use App\Models\Feedback;
use App\Rules\ValidFileContent;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class FeedbackResource extends Resource
{
    protected static ?string $model = Feedback::class;

    protected static ?string $navigationLabel = 'Feedbacks';

    protected static ?string $pluralLabel = 'Feedbacks';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-heart';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->columnSpan(2)
                    ->required(),

                Textarea::make('description')
                    ->columnSpan(2),

                Select::make('user_id')
                    ->columnSpan(2)
                    ->relationship(
                        'user',
                        'name',
                        fn ($query) => $query
                            ->whereRelation('roles', 'name', '=', 'wellness')
                            ->orderBy('name')
                    )
                    ->searchable()
                    ->required(),

                FileUpload::make('file')
                    ->disk('private_feedback')
                    ->maxSize(5120)
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    ])
                    ->rules([
                        'file',
                        'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'max:5120',
                        new ValidFileContent([
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        ]),
                    ])
                    ->helperText('Solo se permiten archivos PDF, DOC y DOCX. Máximo 5MB.')
                    ->getUploadedFileNameForStorageUsing(
                        fn ($file): string => sprintf(
                            'feedback-%s-%s.%s',
                            now()->format('Y-m-d-His'),
                            Str::random(8),
                            $file->getClientOriginalExtension()
                        )
                    ),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()->hasRole('wellness')) {
                    return $query->whereUserId(auth()->user()->id);
                }

                return $query->latest();
            })
            ->recordUrl(null)
            ->defaultSort('created_at', 'desc')
            ->columns([
                IconColumn::make('file')
                    ->label('')
                    ->icon('heroicon-o-arrow-down-on-square')
                    ->url(fn (Feedback $feedback): string => route('secure.feedback', $feedback->id)),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->hidden(fn () => auth()->user()->hasRole('wellness')),
                DeleteAction::make()
                    ->hidden(fn () => auth()->user()->hasRole('wellness')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFeedbacks::route('/'),
            'create' => CreateFeedback::route('/create'),
            'edit' => EditFeedback::route('/{record}/edit'),
        ];
    }
}
