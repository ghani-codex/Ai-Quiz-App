<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LectureResource\Pages;
use App\Models\Lecture;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LectureResource extends Resource
{
    protected static ?string $model = Lecture::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Lecture')
                ->columns(['default' => 1, 'md' => 2])
                ->schema([
                    Forms\Components\Select::make('course_id')
                        ->relationship('course', 'title')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(180),

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
                ]),

            Forms\Components\Section::make('Upload PDF')
                ->visible(fn (Get $get) => $get('content_type') === 'pdf')
                ->description('Text will be extracted automatically on save.')
                ->schema([
                    Forms\Components\FileUpload::make('file_path')
                        ->label('PDF file')
                        ->disk('local')
                        ->directory('lectures')
                        ->acceptedFileTypes(['application/pdf'])
                        ->maxSize(10240)
                        ->preserveFilenames()
                        ->openable()
                        ->downloadable()
                        ->required(fn (Get $get) => $get('content_type') === 'pdf' && empty($get('raw_text'))),
                ]),

            Forms\Components\Section::make('Upload DOCX')
                ->visible(fn (Get $get) => $get('content_type') === 'docx')
                ->description('Text will be extracted automatically on save.')
                ->schema([
                    Forms\Components\FileUpload::make('file_path')
                        ->label('Word document (.docx)')
                        ->disk('local')
                        ->directory('lectures')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                        ->maxSize(10240)
                        ->preserveFilenames()
                        ->openable()
                        ->downloadable()
                        ->required(fn (Get $get) => $get('content_type') === 'docx' && empty($get('raw_text'))),
                ]),

            Forms\Components\Section::make('Paste lecture text')
                ->visible(fn (Get $get) => $get('content_type') === 'text')
                ->schema([
                    Forms\Components\Textarea::make('raw_text')
                        ->label('Lecture content')
                        ->required(fn (Get $get) => $get('content_type') === 'text')
                        ->rows(14)
                        ->maxLength(60000)
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Paste video transcript')
                ->visible(fn (Get $get) => $get('content_type') === 'video_transcript')
                ->schema([
                    Forms\Components\Textarea::make('raw_text')
                        ->label('Transcript')
                        ->required(fn (Get $get) => $get('content_type') === 'video_transcript')
                        ->rows(14)
                        ->maxLength(60000)
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Extracted text')
                ->visible(fn (Get $get, ?Lecture $record) =>
                    $record !== null
                    && in_array($get('content_type'), ['pdf', 'docx'], true)
                    && ! empty(trim((string) $record->raw_text))
                )
                ->description('Auto-extracted on upload. Edit before generating quizzes.')
                ->collapsible()
                ->schema([
                    Forms\Components\Textarea::make('raw_text')
                        ->label('Editable text')
                        ->rows(12)
                        ->maxLength(60000)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('course.title')->searchable()->sortable()->wrap()->limit(40),
                Tables\Columns\TextColumn::make('title')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('content_type')->badge()->colors([
                    'danger'  => 'pdf',
                    'info'    => 'docx',
                    'success' => 'text',
                    'warning' => 'video_transcript',
                ]),
                Tables\Columns\TextColumn::make('quizzes_count')->counts('quizzes')->label('Quizzes'),
                Tables\Columns\TextColumn::make('order')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->since()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('content_type')->options([
                    'pdf' => 'PDF', 'docx' => 'DOCX', 'text' => 'Text', 'video_transcript' => 'Video Transcript',
                ]),
                Tables\Filters\SelectFilter::make('course_id')->relationship('course', 'title'),
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
            'index'  => Pages\ListLectures::route('/'),
            'create' => Pages\CreateLecture::route('/create'),
            'edit'   => Pages\EditLecture::route('/{record}/edit'),
        ];
    }
}
