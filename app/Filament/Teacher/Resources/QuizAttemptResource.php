<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\QuizAttemptResource\Pages;
use App\Models\QuizAttempt;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuizAttemptResource extends Resource
{
    protected static ?string $model = QuizAttempt::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Teaching';

    protected static ?string $navigationLabel = 'Attempts on my quizzes';

    public static function form(Form $form): Form
    {
        return $form;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Student')->searchable(),
                Tables\Columns\TextColumn::make('quiz.title')->label('Quiz')->searchable(),
                Tables\Columns\TextColumn::make('score')->numeric(2)->sortable(),
                Tables\Columns\TextColumn::make('total_points')->numeric(2),
                Tables\Columns\TextColumn::make('time_taken_seconds')->label('Seconds'),
                Tables\Columns\TextColumn::make('submitted_at')->dateTime()->since()->placeholder('—'),
            ])
            ->filters([])
            ->actions([])
            ->bulkActions([])
            ->defaultSort('submitted_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('quiz.lecture.course', fn (Builder $q) => $q->where('teacher_id', auth()->id()));
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuizAttempts::route('/'),
        ];
    }
}
