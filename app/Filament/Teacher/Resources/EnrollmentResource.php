<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\EnrollmentResource\Pages;
use App\Models\Enrollment;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Teaching';

    protected static ?string $navigationLabel = 'My Students';

    public static function form(Form $form): Form
    {
        return $form;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Student')->searchable(),
                Tables\Columns\TextColumn::make('user.email')->label('Email')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('course.title')->label('Course')->searchable(),
                Tables\Columns\TextColumn::make('progress')
                    ->label('Progress')
                    ->state(fn (Enrollment $r) => $r->liveProgress())
                    ->formatStateUsing(fn ($state) => $state . '%')
                    ->badge()
                    ->color(fn ($state) => $state >= 100 ? 'success' : ($state >= 50 ? 'warning' : 'gray'))
                    ->sortable(false),
                Tables\Columns\TextColumn::make('lectures_engagement')
                    ->label('Lectures opened')
                    ->state(function (Enrollment $r) {
                        $lectureIds = $r->course?->lectures()->pluck('id') ?? collect();
                        $total = $lectureIds->count();
                        if ($total === 0) return '—';
                        $opened = \App\Models\LectureView::where('user_id', $r->user_id)
                            ->whereIn('lecture_id', $lectureIds)
                            ->count();
                        return "{$opened} / {$total}";
                    })
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('enrolled_at')->dateTime()->since(),
                Tables\Columns\TextColumn::make('completed_at')->dateTime()->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('course_id')
                    ->label('Course')
                    ->options(fn () => \App\Models\Course::where('teacher_id', auth()->id())->pluck('title', 'id')->all()),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('course', fn (Builder $q) => $q->where('teacher_id', auth()->id()));
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEnrollments::route('/'),
        ];
    }
}
