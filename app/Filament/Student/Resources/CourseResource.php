<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\CourseResource\Pages;
use App\Models\Course;
use App\Models\Enrollment;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Learning';

    protected static ?string $navigationLabel = 'Browse Courses';

    protected static ?string $modelLabel = 'course';

    public static function form(Form $form): Form
    {
        return $form;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')->circular()->size(40),
                Tables\Columns\TextColumn::make('title')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('category.name')->badge()->sortable(),
                Tables\Columns\TextColumn::make('teacher.name')->label('Instructor')->searchable(),
                Tables\Columns\TextColumn::make('level')->badge()->colors([
                    'gray'    => 'beginner',
                    'warning' => 'intermediate',
                    'danger'  => 'advanced',
                ]),
                Tables\Columns\TextColumn::make('lectures_count')->label('Lectures'),
                Tables\Columns\IconColumn::make('is_enrolled')
                    ->label('Enrolled?')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')->relationship('category', 'name')->label('Category'),
                Tables\Filters\SelectFilter::make('level')->options([
                    'beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced',
                ]),
            ])
            ->actions([
                Tables\Actions\Action::make('enroll')
                    ->label('Enroll')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Course $r) => ! ($r->is_enrolled ?? false))
                    ->action(function (Course $r) {
                        Enrollment::firstOrCreate(
                            ['user_id' => auth()->id(), 'course_id' => $r->id],
                            ['enrolled_at' => now(), 'progress_pct' => 0],
                        );
                        Notification::make()
                            ->title('Enrolled successfully')
                            ->body("You're now enrolled in {$r->title}.")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\ViewAction::make()->label('Details'),
            ])
            ->bulkActions([])
            ->defaultSort('is_featured', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $userId = auth()->id();

        return parent::getEloquentQuery()
            ->where('is_published', true)
            ->with(['category:id,name', 'teacher:id,name'])
            ->withCount('lectures')
            ->withExists(['enrollments as is_enrolled' => fn (Builder $q) => $q->where('user_id', $userId)]);
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'view'  => Pages\ViewCourse::route('/{record}'),
        ];
    }
}
