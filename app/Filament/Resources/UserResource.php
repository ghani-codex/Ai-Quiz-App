<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Users';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Account')
                ->columns(['default' => 1, 'md' => 2])
                ->schema([
                    Forms\Components\TextInput::make('name')->required()->maxLength(255),
                    Forms\Components\TextInput::make('email')->email()->required()->maxLength(255)->unique(ignoreRecord: true),
                    Forms\Components\Select::make('role')
                        ->options([
                            User::ROLE_ADMIN   => 'Admin',
                            User::ROLE_TEACHER => 'Teacher',
                            User::ROLE_STUDENT => 'Student',
                        ])->required(),
                    Forms\Components\Toggle::make('is_approved')
                        ->helperText('Teachers need approval before they can use the dashboard'),
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $operation) => $operation === 'create')
                        ->maxLength(255)
                        ->revealable(),
                ]),
            Forms\Components\Section::make('Profile')
                ->columns(['default' => 1, 'md' => 2])
                ->schema([
                    Forms\Components\FileUpload::make('avatar_path')->image()->avatar()->directory('avatars')->maxSize(2048),
                    Forms\Components\TextInput::make('country')->maxLength(80),
                    Forms\Components\Textarea::make('bio')->rows(3)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_path')->circular()->size(40)->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?background=4154f1&color=fff&name=U'),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->copyable()->toggleable(),
                Tables\Columns\TextColumn::make('role')->badge()->colors([
                    'danger'  => User::ROLE_ADMIN,
                    'warning' => User::ROLE_TEACHER,
                    'gray'    => User::ROLE_STUDENT,
                ]),
                Tables\Columns\IconColumn::make('is_approved')->boolean()->label('Approved'),
                Tables\Columns\TextColumn::make('country')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->since()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')->options([
                    User::ROLE_ADMIN   => 'Admin',
                    User::ROLE_TEACHER => 'Teacher',
                    User::ROLE_STUDENT => 'Student',
                ]),
                Tables\Filters\TernaryFilter::make('is_approved')->label('Approved'),
                Tables\Filters\Filter::make('pending_teachers')
                    ->label('Pending teacher approvals only')
                    ->query(fn ($query) => $query->where('role', User::ROLE_TEACHER)->where('is_approved', false))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (User $r) => $r->role === User::ROLE_TEACHER && ! $r->is_approved)
                    ->requiresConfirmation()
                    ->action(function (User $r) {
                        $r->update(['is_approved' => true]);
                        Notification::make()->title("Approved {$r->name}")->success()->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Approve selected')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $records->each(fn (User $u) => $u->update(['is_approved' => true]));
                            Notification::make()->title('Approved '.$records->count().' users')->success()->send();
                        }),
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
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
