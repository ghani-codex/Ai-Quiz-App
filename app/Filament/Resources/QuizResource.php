<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuizResource\Pages;
use App\Models\Quiz;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuizResource extends Resource
{
    protected static ?string $model = Quiz::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Quiz settings')
                ->columns(['default' => 1, 'md' => 2])
                ->schema([
                    Forms\Components\Select::make('lecture_id')
                        ->relationship('lecture', 'title')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(180),

                    Forms\Components\Select::make('difficulty')
                        ->options([
                            'easy'   => 'Easy',
                            'medium' => 'Medium',
                            'hard'   => 'Hard',
                        ])
                        ->default('medium')
                        ->required(),

                    Forms\Components\TextInput::make('question_count')
                        ->numeric()
                        ->minValue(3)
                        ->maxValue(50)
                        ->default(10)
                        ->required(),

                    Forms\Components\TextInput::make('time_limit_minutes')
                        ->label('Time limit (minutes)')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(180)
                        ->default(15)
                        ->required(),

                    Forms\Components\Toggle::make('is_published')
                        ->helperText('Visible to enrolled students'),
                ]),

            Forms\Components\Section::make('Agent generation')
                ->columns(['default' => 1, 'md' => 2])
                ->schema([
                    Forms\Components\Select::make('ai_generation_status')
                        ->options([
                            'pending'    => 'Pending',
                            'processing' => 'Processing',
                            'ready'      => 'Ready',
                            'failed'     => 'Failed',
                        ])
                        ->disabled()
                        ->dehydrated(false),

                    Forms\Components\TextInput::make('ai_provider_used')
                        ->disabled()
                        ->dehydrated(false),

                    Forms\Components\TextInput::make('ai_tokens_used')
                        ->numeric()
                        ->disabled()
                        ->dehydrated(false),

                    Forms\Components\DateTimePicker::make('ai_generated_at')
                        ->disabled()
                        ->dehydrated(false),
                ])
                ->collapsible()
                ->collapsed()
                ->hiddenOn('create'),
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
                Tables\Columns\TextColumn::make('question_count')->label('Qs'),
                Tables\Columns\TextColumn::make('time_limit_minutes')->label('Min'),
                Tables\Columns\TextColumn::make('ai_generation_status')->badge()->colors([
                    'gray'    => 'pending',
                    'warning' => 'processing',
                    'success' => 'ready',
                    'danger'  => 'failed',
                ]),
                Tables\Columns\IconColumn::make('is_published')->boolean()->label('Live'),
                Tables\Columns\TextColumn::make('created_at')->since()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('difficulty')->options([
                    'easy' => 'Easy', 'medium' => 'Medium', 'hard' => 'Hard',
                ]),
                Tables\Filters\SelectFilter::make('ai_generation_status')->options([
                    'pending' => 'Pending', 'processing' => 'Processing', 'ready' => 'Ready', 'failed' => 'Failed',
                ]),
                Tables\Filters\TernaryFilter::make('is_published'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
