<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\QuizResource\Pages;
use App\Filament\Teacher\Resources\QuizResource\RelationManagers\QuestionsRelationManager;
use App\Models\Quiz;
use App\Services\AI\QuizGeneratorService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuizResource extends Resource
{
    protected static ?string $model = Quiz::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $navigationGroup = 'Teaching';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Quiz settings')
                ->columns(['default' => 1, 'md' => 2])
                ->schema([
                    Forms\Components\Select::make('lecture_id')
                        ->label('Lecture (Agent will read this lecture\'s text)')
                        ->options(fn () => \App\Models\Lecture::query()
                            ->whereHas('course', fn ($c) => $c->where('teacher_id', auth()->id()))
                            ->with('course:id,title')
                            ->orderBy('course_id')
                            ->orderBy('order')
                            ->get()
                            ->mapWithKeys(fn ($l) => [$l->id => ($l->course?->title ?? 'Course') . ' — ' . $l->title])
                            ->all())
                        ->searchable()
                        ->required(),
                    Forms\Components\TextInput::make('title')->required()->maxLength(180),
                    Forms\Components\Select::make('difficulty')->options([
                        'easy'   => 'Easy',
                        'medium' => 'Medium',
                        'hard'   => 'Hard',
                    ])->default('medium')->required(),
                    Forms\Components\TextInput::make('question_count')
                        ->numeric()->minValue(3)->maxValue(50)->default(10)->required()
                        ->helperText('How many questions to generate'),
                    Forms\Components\TextInput::make('time_limit_minutes')
                        ->label('Time limit (minutes)')
                        ->numeric()->minValue(1)->maxValue(180)->default(15)->required(),
                    Forms\Components\Toggle::make('is_published')
                        ->helperText('Turn on only after you have reviewed the Agent-generated questions'),
                ]),

            Forms\Components\Placeholder::make('ai-hint')
                ->label('How to generate questions')
                ->content('After saving, you will land on the edit page where a "✨ Generate with Agent" button appears. Clicking it reads the selected lecture text and produces questions automatically.')
                ->visibleOn('create'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->wrap()->limit(40),
                Tables\Columns\TextColumn::make('lecture.title')->label('Lecture')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('lecture.course.title')->label('Course')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('difficulty')->badge()->colors([
                    'success' => 'easy',
                    'warning' => 'medium',
                    'danger'  => 'hard',
                ]),
                Tables\Columns\TextColumn::make('questions_count')
                    ->counts('questions')
                    ->label('Questions')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('time_limit_minutes')->label('Min'),
                Tables\Columns\TextColumn::make('ai_generation_status')->badge()->colors([
                    'gray'    => 'pending',
                    'warning' => 'processing',
                    'success' => 'ready',
                    'danger'  => 'failed',
                ]),
                Tables\Columns\IconColumn::make('is_published')->boolean()->label('Live'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('ai_generation_status')->options([
                    'pending'    => 'Pending',
                    'processing' => 'Processing',
                    'ready'      => 'Ready',
                    'failed'     => 'Failed',
                ]),
                Tables\Filters\TernaryFilter::make('is_published'),
            ])
            ->actions([
                Tables\Actions\Action::make('generate_ai')
                    ->label('Generate with Agent')
                    ->icon('heroicon-o-sparkles')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Generate quiz with Agent')
                    ->modalDescription('This reads the lecture text and produces multiple-choice questions. Any existing questions on this quiz will be REPLACED.')
                    ->modalSubmitActionLabel('Generate')
                    ->action(fn (Quiz $record) => self::runAiGeneration($record)),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function runAiGeneration(Quiz $quiz): void
    {
        try {
            $count = app(QuizGeneratorService::class)->generate($quiz);

            Notification::make()
                ->title('Quiz generated successfully')
                ->body("{$count} questions written from the lecture. Review them on the Questions tab.")
                ->success()
                ->duration(8000)
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Agent generation failed')
                ->body($e->getMessage())
                ->danger()
                ->duration(10000)
                ->send();
        }
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('lecture.course', fn (Builder $q) => $q->where('teacher_id', auth()->id()));
    }

    public static function getRelations(): array
    {
        return [
            QuestionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListQuizzes::route('/'),
            'create' => Pages\CreateQuiz::route('/create'),
            'edit'   => Pages\EditQuiz::route('/{record}/edit'),
        ];
    }
}
