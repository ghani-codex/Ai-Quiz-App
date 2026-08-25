<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Models\Course;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Basics')
                        ->icon('heroicon-o-information-circle')
                        ->columns(['default' => 1, 'md' => 2])
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->required()
                                ->maxLength(180)
                                ->live(onBlur: true)
                                ->columnSpanFull(),

                            Forms\Components\Select::make('teacher_id')
                                ->relationship('teacher', 'name', fn ($query) => $query->where('role', User::ROLE_TEACHER)->where('is_approved', true))
                                ->searchable()
                                ->preload()
                                ->required()
                                ->helperText('Only approved teachers'),

                            Forms\Components\Select::make('category_id')
                                ->relationship('category', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('name')->required(),
                                    Forms\Components\Textarea::make('description'),
                                ]),

                            Forms\Components\Select::make('level')
                                ->options([
                                    'beginner'     => 'Beginner',
                                    'intermediate' => 'Intermediate',
                                    'advanced'     => 'Advanced',
                                ])
                                ->default('beginner')
                                ->required(),

                            Forms\Components\TextInput::make('price')
                                ->numeric()
                                ->minValue(0)
                                ->default(0)
                                ->prefix('$')
                                ->helperText('Display-only for v1 — no payment integration yet.'),

                            Forms\Components\Textarea::make('description')
                                ->required()
                                ->rows(6)
                                ->columnSpanFull(),
                        ]),

                    Wizard\Step::make('Media')
                        ->icon('heroicon-o-photo')
                        ->schema([
                            Forms\Components\FileUpload::make('thumbnail')
                                ->image()
                                ->imageEditor()
                                ->directory('courses/thumbnails')
                                ->maxSize(2048)
                                ->helperText('400x250 recommended. Max 2 MB.'),
                        ]),

                    Wizard\Step::make('Publishing')
                        ->icon('heroicon-o-rocket-launch')
                        ->columns(['default' => 1, 'md' => 2])
                        ->schema([
                            Forms\Components\Toggle::make('is_published')
                                ->label('Publish to catalog')
                                ->helperText('Visible to students once turned on.')
                                ->onIcon('heroicon-m-check')
                                ->offIcon('heroicon-m-x-mark'),

                            Forms\Components\Toggle::make('is_featured')
                                ->label('Feature on homepage')
                                ->helperText('Show in the Featured Courses carousel.'),
                        ]),
                ])->skippable()->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')->circular()->size(40),
                Tables\Columns\TextColumn::make('title')->searchable()->wrap()->limit(40),
                Tables\Columns\TextColumn::make('teacher.name')->label('Instructor')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('category.name')->badge()->sortable(),
                Tables\Columns\TextColumn::make('level')->badge()->colors([
                    'gray'    => 'beginner',
                    'warning' => 'intermediate',
                    'danger'  => 'advanced',
                ]),
                Tables\Columns\TextColumn::make('lectures_count')->counts('lectures')->label('Lectures'),
                Tables\Columns\TextColumn::make('enrollments_count')->counts('enrollments')->label('Students'),
                Tables\Columns\IconColumn::make('is_published')->boolean()->label('Live'),
                Tables\Columns\IconColumn::make('is_featured')->boolean()->label('Featured'),
                Tables\Columns\TextColumn::make('created_at')->since()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')->label('Published'),
                Tables\Filters\TernaryFilter::make('is_featured')->label('Featured'),
                Tables\Filters\SelectFilter::make('category_id')->relationship('category', 'name')->label('Category'),
                Tables\Filters\SelectFilter::make('level')->options([
                    'beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced',
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit'   => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
