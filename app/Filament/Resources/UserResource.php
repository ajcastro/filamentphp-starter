<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    private const PASSWORD_LENGTH = 8;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('email')->required()->email(),
                TextInput::make('password')->required()->readOnly()->hidden(fn($record) => $record !== null)
                    ->default(Str::random(static::PASSWORD_LENGTH)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->sortable(),
                TextColumn::make('email_verified_at')->sortable()->dateTime(),
                IconColumn::make('is_admin')->boolean()->alignCenter(),
                IconColumn::make('is_enabled')->boolean()->alignCenter(),
                TextColumn::make('disabled_at')->sortable()->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                ActionGroup::make([
                    Action::make('reset_password')
                        ->requiresConfirmation()
                        ->icon('gmdi-restart-alt')
                        ->action(function (User $record) {
                            $newPassword = Str::random(static::PASSWORD_LENGTH);
                            $record->password = bcrypt($newPassword);

                            Notification::make()
                                ->title('Password reset is successful!')
                                ->body(Str::markdown("Your new password is **{$newPassword}**"))
                                ->success()
                                ->send();
                        }),
                    Action::make('enable')
                        ->hidden(fn(User $record) => $record->disabled_at === null)
                        ->requiresConfirmation()
                        ->icon('gmdi-check-circle-outline-tt')
                        ->action(function (User $record) {
                            $record->disabled_at = null;
                            $record->save();

                            Notification::make()
                                ->title('Successfully Enabled!')
                                ->success()
                                ->send();
                        }),
                    Action::make('disable')
                        ->hidden(fn(User $record) => $record->disabled_at !== null)
                        ->requiresConfirmation()
                        ->icon('gmdi-block')
                        ->action(function (User $record) {
                            $record->disabled_at = now();
                            $record->save();

                            Notification::make()
                                ->title('Successfully Disabled!')
                                ->success()
                                ->send();
                        }),
                    Action::make('grant_admin')
                        ->hidden(fn(User $record) => $record->is_admin)
                        ->requiresConfirmation()
                        ->icon('gmdi-check-circle-outline-tt')
                        ->action(function (User $record) {
                            $record->is_admin = true;
                            $record->save();

                            Notification::make()
                                ->title('Admin rights granted!')
                                ->success()
                                ->send();
                        }),
                    Action::make('revoke_admin')
                        ->hidden(fn(User $record) => !$record->is_admin)
                        ->requiresConfirmation()
                        ->icon('gmdi-block')
                        ->action(function (User $record) {
                            $record->is_admin = false;
                            $record->save();

                            Notification::make()
                                ->title('Admin rights revoked!')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
