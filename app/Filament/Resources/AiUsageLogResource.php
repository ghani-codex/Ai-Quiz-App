<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AiUsageLogResource\Pages;
use App\Filament\Resources\AiUsageLogResource\RelationManagers;
use App\Models\AiUsageLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AiUsageLogResource extends Resource
{
    protected static ?string $model = AiUsageLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationGroup = 'Agent Insights';

    protected static ?string $navigationLabel = 'Agent Usage Logs';

    protected static ?string $modelLabel = 'agent usage log';

    protected static ?string $pluralModelLabel = 'agent usage logs';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('provider')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('model')
                    ->required()
                    ->maxLength(60),
                Forms\Components\TextInput::make('input_tokens')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('output_tokens')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->default(null),
                Forms\Components\Select::make('related_quiz_id')
                    ->relationship('relatedQuiz', 'title')
                    ->default(null),
                Forms\Components\Textarea::make('error_message')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('latency_ms')
                    ->numeric()
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('provider')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('input_tokens')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('output_tokens')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('relatedQuiz.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('latency_ms')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAiUsageLogs::route('/'),
            'create' => Pages\CreateAiUsageLog::route('/create'),
            'edit' => Pages\EditAiUsageLog::route('/{record}/edit'),
        ];
    }
}
