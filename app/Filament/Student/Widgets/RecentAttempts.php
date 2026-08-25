<?php

namespace App\Filament\Student\Widgets;

use App\Models\QuizAttempt;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentAttempts extends BaseWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'My recent attempts';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                QuizAttempt::query()
                    ->where('user_id', auth()->id())
                    ->latest('submitted_at')
                    ->limit(8)
            )
            ->columns([
                Tables\Columns\TextColumn::make('quiz.title')->label('Quiz')->wrap(),
                Tables\Columns\TextColumn::make('quiz.lecture.course.title')->label('Course')->toggleable(),
                Tables\Columns\TextColumn::make('score')
                    ->badge()
                    ->numeric(1)
                    ->color(fn ($state) => $state === null ? 'gray' : ($state >= 70 ? 'success' : ($state >= 50 ? 'warning' : 'danger'))),
                Tables\Columns\TextColumn::make('submitted_at')->since()->placeholder('Not submitted'),
            ])
            ->paginated(false);
    }
}
