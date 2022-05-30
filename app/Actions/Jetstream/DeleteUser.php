<?php

declare(strict_types=1);

namespace App\Actions\Jetstream;

use Illuminate\Support\Facades\DB;
use Laravel\Jetstream\Contracts\DeletesUsers;

/**
 * Class DeleteUser.
 */
class DeleteUser implements DeletesUsers
{
    /**
     * Delete the given user.
     *
     * @param  mixed  $user
     * @return void
     */
    public function delete($user): void
    {
        DB::transaction(function () use ($user) {
            $user->tokens->each->delete();
            $user->delete();
        });
    }
}
