<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\EnrollmentResource\Pages;
use App\Models\Enrollment;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static ?string $navigationIcon = 'heroicon-o-bookmark';

    protected static ?string $navigationGroup = 'Learning';

    protected static ?string $navigationLabel = 'My Courses';

    public static function form(Form $form): Form
    {
        return $form;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('course.thumbnail')->circular()->size(40),
                Tables\Columns\TextColumn::make('course.title')->label('Course')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('course.teacher.name')->label('Instructor'),
                Tables\Columns\TextColumn::make('progress')
                    ->label('Progress')
                    ->state(fn (Enrollment $r) => $r->liveProgress())
                    ->formatStateUsing(fn ($state) => $state . '%')
                    ->badge()
                    ->color(fn ($state) => $state >= 100 ? 'success' : ($state >= 50 ? 'warning' : 'gray')),
                Tables\Columns\TextColumn::make('enrolled_at')->since(),
                Tables\Columns\TextColumn::make('completed_at')->dateTime()->placeholder('In progress'),
            ])
            ->actions([
                Tables\Actions\Action::make('learn')
                    ->label('Open Course')
                    ->icon('heroicon-o-book-open')
                    ->color('primary')
                    ->size('lg')
                    ->url(fn (Enrollment $r) => \App\Filament\Student\Pages\CoursePlayer::getUrl(['course' => $r->course_id])),
            ])
            ->bulkActions([])
            ->defaultSort('enrolled_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
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
