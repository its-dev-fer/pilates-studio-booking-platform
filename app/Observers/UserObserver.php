<?php

namespace App\Observers;

use App\Mail\WelcomeUserMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        if ($user->plain_password) {
            Mail::to($user->email)->send(new WelcomeUserMail($user, $user->plain_password));
        }
    }

    public function creating(User $user): void
    {
        // Si no se le asignó una contraseña manual, le generamos una aleatoria de 10 caracteres
        if (empty($user->password)) {
            $user->plain_password = Str::password(10, true, true, true, false);
            $user->password = Hash::make($user->plain_password);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
