<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\QuizAttemptResource\Pages;
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

    protected static ?string $navigationGroup = 'Learning';

    protected static ?string $navigationLabel = 'My Attempts';

    public static function form(Form $form): Form
    {
        return $form;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('quiz.title')->label('Quiz')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('quiz.lecture.course.title')->label('Course')->toggleable(),
                Tables\Columns\TextColumn::make('score')
                    ->numeric(2)
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state === null ? 'gray' : ($state >= 70 ? 'success' : ($state >= 50 ? 'warning' : 'danger'))),
                Tables\Columns\TextColumn::make('total_points')->numeric(2)->label('Out of'),
                Tables\Columns\TextColumn::make('time_taken_seconds')->label('Seconds')->suffix('s'),
                Tables\Columns\TextColumn::make('submitted_at')->dateTime()->since()->placeholder('Not submitted'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('view_results')
                    ->label('View results')
                    ->icon('heroicon-o-chart-bar')
                    ->color('primary')
                    ->url(fn (QuizAttempt $r) => \App\Filament\Student\Pages\QuizResults::getUrl(['attempt' => $r->id]))
                    ->visible(fn (QuizAttempt $r) => $r->submitted_at !== null),
                Tables\Actions\Action::make('resume')
                    ->label('Resume')
                    ->icon('heroicon-o-play')
                    ->color('warning')
                    ->url(fn (QuizAttempt $r) => \App\Filament\Student\Pages\TakeQuiz::getUrl(['quiz' => $r->quiz_id]))
                    ->visible(fn (QuizAttempt $r) => $r->submitted_at === null),
            ])
            ->bulkActions([])
            ->defaultSort('submitted_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id())
            ->with([
                'quiz:id,title,lecture_id',
                'quiz.lecture:id,title,course_id',
                'quiz.lecture.course:id,title',
            ]);
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
