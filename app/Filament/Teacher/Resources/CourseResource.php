<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\CourseResource\Pages;
use App\Filament\Teacher\Resources\CourseResource\RelationManagers;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Teaching';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'My Course';

    protected static ?string $navigationLabel = 'My Courses';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Basics')
                        ->icon('heroicon-o-information-circle')
                        ->description('Title, category, level, price')
                        ->columns(['default' => 1, 'md' => 2])
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->required()
                                ->maxLength(180)
                                ->live(onBlur: true)
                                ->columnSpanFull(),

                            Forms\Components\Select::make('category_id')
                                ->relationship('category', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),

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
                                ->helperText('Display only for v1.'),

                            Forms\Components\Textarea::make('description')
                                ->required()
                                ->rows(6)
                                ->columnSpanFull()
                                ->helperText('What will students learn? A short paragraph works best.'),
                        ]),

                    Wizard\Step::make('Cover image')
                        ->icon('heroicon-o-photo')
                        ->description('Optional thumbnail for the catalog')
                        ->schema([
                            Forms\Components\FileUpload::make('thumbnail')
                                ->image()
                                ->imageEditor()
                                ->directory('courses/thumbnails')
                                ->maxSize(2048)
                                ->helperText('400x250 recommended. Max 2 MB. You can add this later.'),
                        ]),

                    Wizard\Step::make('Publish')
                        ->icon('heroicon-o-rocket-launch')
                        ->description('When you are ready, flip the switch')
                        ->columns(['default' => 1, 'md' => 2])
                        ->schema([
                            Forms\Components\Toggle::make('is_published')
                                ->label('Publish to catalog')
                                ->helperText('Visible to students once turned on.')
                                ->onIcon('heroicon-m-check')
                                ->offIcon('heroicon-m-x-mark'),

                            Forms\Components\Toggle::make('is_featured')
                                ->label('Suggest as featured')
                                ->helperText('Admin approves featuring on the homepage.'),

                            Forms\Components\Placeholder::make('next-steps')
                                ->label('What happens next')
                                ->content('After saving, add Lectures from the course detail page. Each lecture can have one or more Agent-generated quizzes.')
                                ->columnSpanFull(),
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
                Tables\Filters\SelectFilter::make('category_id')->relationship('category', 'name')->label('Category'),
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
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Create your first course')
            ->emptyStateDescription('Step through the wizard — title, cover, publish. Add lectures next.')
            ->emptyStateIcon('heroicon-o-rectangle-stack')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('teacher_id', auth()->id());
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\LecturesRelationManager::class,
        ];
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
