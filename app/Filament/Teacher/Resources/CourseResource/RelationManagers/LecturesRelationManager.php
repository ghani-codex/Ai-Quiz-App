<?php

namespace App\Filament\Teacher\Resources\CourseResource\RelationManagers;

use App\Models\Lecture;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class LecturesRelationManager extends RelationManager
{
    protected static string $relationship = 'lectures';

    protected static ?string $title = 'Lectures';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required()->maxLength(180),

            Forms\Components\Select::make('content_type')
                ->options([
                    'pdf'              => 'PDF Upload',
                    'docx'             => 'DOCX Upload',
                    'text'             => 'Pasted Text',
                    'video_transcript' => 'Video Transcript',
                ])
                ->default('text')
                ->required()
                ->live(),

            Forms\Components\TextInput::make('order')->numeric()->default(0),

            Forms\Components\FileUpload::make('file_path')
                ->label('PDF file')
                ->visible(fn (Get $get) => $get('content_type') === 'pdf')
                ->required(fn (Get $get) => $get('content_type') === 'pdf' && empty($get('raw_text')))
                ->disk('local')
                ->directory('lectures')
                ->acceptedFileTypes(['application/pdf'])
                ->maxSize(10240)
                ->preserveFilenames()
                ->columnSpanFull()
                ->helperText('Text will be extracted on save.'),

            Forms\Components\FileUpload::make('file_path')
                ->label('Word document (.docx)')
                ->visible(fn (Get $get) => $get('content_type') === 'docx')
                ->required(fn (Get $get) => $get('content_type') === 'docx' && empty($get('raw_text')))
                ->disk('local')
                ->directory('lectures')
                ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                ->maxSize(10240)
                ->preserveFilenames()
                ->columnSpanFull()
                ->helperText('Text will be extracted on save.'),

            Forms\Components\Textarea::make('raw_text')
                ->label(fn (Get $get) => match ($get('content_type')) {
                    'text'             => 'Lecture content',
                    'video_transcript' => 'Transcript',
                    default            => 'Extracted / fallback text',
                })
                ->visible(fn (Get $get, ?Lecture $record) =>
                    in_array($get('content_type'), ['text', 'video_transcript'], true)
                    || ($record !== null && in_array($get('content_type'), ['pdf', 'docx'], true))
                )
                ->required(fn (Get $get) => in_array($get('content_type'), ['text', 'video_transcript'], true))
                ->rows(10)
                ->maxLength(60000)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('order')->sortable()->label('#'),
                Tables\Columns\TextColumn::make('title')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('content_type')->badge(),
                Tables\Columns\TextColumn::make('quizzes_count')->counts('quizzes')->label('Quizzes'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('order');
    }
}
