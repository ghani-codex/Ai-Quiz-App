<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\AvailableQuizResource\Pages;
use App\Models\Quiz;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AvailableQuizResource extends Resource
{
    protected static ?string $model = Quiz::class;

    protected static ?string $navigationIcon = 'heroicon-o-play-circle';

    protected static ?string $navigationGroup = 'Learning';

    protected static ?string $navigationLabel = 'Take a Quiz';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'quiz';

    protected static ?string $pluralModelLabel = 'available quizzes';

    public static function form(Form $form): Form
    {
        return $form;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('lecture.course.title')->label('Course')->searchable()->wrap()->limit(40),
                Tables\Columns\TextColumn::make('lecture.title')->label('Lecture')->searchable()->wrap()->limit(40),
                Tables\Columns\TextColumn::make('title')->label('Quiz')->searchable()->wrap()->weight('bold'),
                Tables\Columns\TextColumn::make('difficulty')->badge()->colors([
                    'success' => 'easy',
                    'warning' => 'medium',
                    'danger'  => 'hard',
                ]),
                Tables\Columns\TextColumn::make('questions_count')
                    ->label('Questions')
                    ->badge(),
                Tables\Columns\TextColumn::make('time_limit_minutes')->label('Time')->suffix(' min'),
                Tables\Columns\TextColumn::make('my_attempts_count')
                    ->label('My attempts')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'gray' : 'primary')
                    ->formatStateUsing(fn ($state) => (int) ($state ?? 0)),
                Tables\Columns\TextColumn::make('my_best_score')
                    ->label('Best score')
                    ->badge()
                    ->color(fn ($state) => $state === null ? 'gray' : ($state >= 70 ? 'success' : ($state >= 50 ? 'warning' : 'danger')))
                    ->formatStateUsing(fn ($state) => $state !== null ? number_format($state, 1) . '/100' : '—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('difficulty')->options([
                    'easy'   => 'Easy',
                    'medium' => 'Medium',
                    'hard'   => 'Hard',
                ]),
            ])
            ->actions([
                Tables\Actions\Action::make('take')
                    ->label('Take Quiz')
                    ->icon('heroicon-o-play')
                    ->color('primary')
                    ->size('lg')
                    ->url(fn (Quiz $r) => \App\Filament\Student\Pages\TakeQuiz::getUrl(['quiz' => $r->id])),
            ])
            ->bulkActions([])
            ->emptyStateHeading('No quizzes available yet')
            ->emptyStateDescription('Enroll in a course from "Browse Courses" — once your teacher publishes a quiz it will appear here.')
            ->emptyStateIcon('heroicon-o-puzzle-piece');
    }

    public static function getEloquentQuery(): Builder
    {
        $userId = auth()->id();

        return parent::getEloquentQuery()
            ->where('is_published', true)
            ->where('ai_generation_status', 'ready')
            ->whereHas('questions')
            ->whereHas('lecture.course.enrollments', fn (Builder $q) => $q->where('user_id', $userId))
            ->with(['lecture:id,title,course_id', 'lecture.course:id,title'])
            ->withCount([
                'questions',
                'attempts as my_attempts_count' => fn (Builder $q) => $q->where('user_id', $userId)->whereNotNull('submitted_at'),
            ])
            ->withMax(
                ['attempts as my_best_score' => fn (Builder $q) => $q->where('user_id', $userId)->whereNotNull('score')],
                'score'
            );
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAvailableQuizzes::route('/'),
        ];
    }
}
