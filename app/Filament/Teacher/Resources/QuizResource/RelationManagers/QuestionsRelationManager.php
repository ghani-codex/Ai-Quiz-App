<?php

namespace App\Filament\Teacher\Resources\QuizResource\RelationManagers;

use App\Models\Question;
use App\Models\QuestionOption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    protected static ?string $title = 'Questions';

    protected static ?string $recordTitleAttribute = 'text';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Textarea::make('text')
                ->required()
                ->rows(3)
                ->columnSpanFull()
                ->label('Question'),

            Forms\Components\Hidden::make('type')->default('mcq'),

            Forms\Components\Textarea::make('explanation')
                ->rows(2)
                ->columnSpanFull()
                ->helperText('Why is the correct answer correct? Shown to students after submission.'),

            Forms\Components\Repeater::make('options')
                ->relationship('options')
                ->label('Options (exactly 4 — mark one as correct)')
                ->schema([
                    Forms\Components\TextInput::make('text')->required()->maxLength(500),
                    Forms\Components\Toggle::make('is_correct')->label('Correct'),
                    Forms\Components\Hidden::make('order')->default(0),
                ])
                ->minItems(4)
                ->maxItems(4)
                ->defaultItems(4)
                ->columnSpanFull()
                ->columns(['default' => 1, 'md' => 2])
                ->reorderable(false),

            Forms\Components\TextInput::make('points')->numeric()->default(1),
            Forms\Components\TextInput::make('order')->numeric()->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('text')
            ->columns([
                Tables\Columns\TextColumn::make('order')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('text')->wrap()->limit(80)->searchable(),
                Tables\Columns\TextColumn::make('options_count')
                    ->counts('options')
                    ->label('Options')
                    ->badge(),
                Tables\Columns\TextColumn::make('correct_option_text')
                    ->label('Correct answer')
                    ->state(fn (Question $r) => $r->options->firstWhere('is_correct', true)?->text ?? '—')
                    ->limit(50)
                    ->wrap(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->mutateFormDataUsing(function (array $data) {
                    $data['type'] = 'mcq';
                    return $data;
                }),
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
            ->defaultSort('order')
            ->emptyStateHeading('No questions yet')
            ->emptyStateDescription('Use the “Generate with Agent” button on the quiz edit page, or add questions manually.')
            ->emptyStateIcon('heroicon-o-puzzle-piece');
    }
}
