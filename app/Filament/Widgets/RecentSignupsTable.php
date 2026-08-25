<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentSignupsTable extends BaseWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Recent signups';

    protected int|string|array $columnSpan = ['md' => 1, 'xl' => 1];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()->latest()->limit(8)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_path')
                    ->circular()
                    ->size(36)
                    ->defaultImageUrl(fn (User $r) => 'https://ui-avatars.com/api/?background=4154f1&color=fff&size=64&name=' . urlencode($r->name)),
                Tables\Columns\TextColumn::make('name')->wrap(),
                Tables\Columns\TextColumn::make('role')->badge()->colors([
                    'danger'  => User::ROLE_ADMIN,
                    'warning' => User::ROLE_TEACHER,
                    'gray'    => User::ROLE_STUDENT,
                ]),
                Tables\Columns\IconColumn::make('is_approved')->boolean()->label('OK'),
                Tables\Columns\TextColumn::make('created_at')->since()->label('Joined'),
            ])
            ->paginated(false);
    }
}
