<?php

namespace App\Filament\Resources\Users\Tables;

use App\Mail\WelcomeUserMail;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('last_name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('roles.name')
                    ->label('Perfil')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'empleado' => 'warning',
                        'cliente' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('tenants.name')
                    ->label('Sucursales')
                    ->badge()
                    ->limitList(2),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('resendCredentials')
                    ->label('Reenviar credenciales')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Reenviar credenciales de acceso')
                    ->modalDescription('Se generará una nueva contraseña temporal, se reemplazará la actual y se enviará al correo del usuario.')
                    ->action(function (User $record): void {
                        $plainPassword = Str::password(8, true, true, true, false);

                        DB::transaction(function () use ($record, $plainPassword): void {
                            $record->forceFill([
                                'password' => Hash::make($plainPassword),
                            ])->save();

                            Mail::to($record->email)->send(new WelcomeUserMail($record->fresh(), $plainPassword, true));
                        });

                        Notification::make()
                            ->title('Credenciales reenviadas')
                            ->body('Se generó una nueva contraseña y se envió al correo del usuario.')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
