<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeedbackResource\Pages;
use App\Models\Feedback;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class FeedbackResource extends Resource
{
    protected static ?string $model = Feedback::class;

    protected static ?string $slug = 'feedback';

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                        fn($query) => $query
                            ->whereRelation('roles', 'name', '=', 'wellness')
                            ->orderBy('name')
                    )
                    ->searchable()
                    ->required(),

                FileUpload::make('file')
                    ->disk('public')
                    ->rule([
                        'file',
                        'mimes:pdf,doc,docx',
                        'max:1024'
                    ])->helperText('Only PDF, DOC and DOCX files are allowed.'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()->hasRole('wellness')) {
                    return $query->whereUserId(auth()->user()->id);
                }

                return $query->orderBy('created_at', 'desc');
            })
            ->defaultSort('created_at', 'desc')
            ->columns([
                IconColumn::make('file')
                    ->label('')
                    ->icon('heroicon-o-arrow-down-on-square')
                    ->url(fn(Feedback $feedback
                    ) => Storage::disk('public')->url($feedback->file),
                        true),

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
            ->actions([
                EditAction::make()
                    ->hidden(fn() => auth()->user()->hasRole('wellness')),
                DeleteAction::make()
                    ->hidden(fn() => auth()->user()->hasRole('wellness')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeedbacks::route('/'),
            'create' => Pages\CreateFeedback::route('/create'),
            'edit' => Pages\EditFeedback::route('/{record}/edit'),
        ];
    }
}
