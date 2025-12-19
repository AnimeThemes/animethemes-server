<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Fortify\Fortify;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');

                $table->text('two_factor_secret')
                    ->nullable();

                $table->text('two_factor_recovery_codes')
                    ->nullable();

                if (Fortify::confirmsTwoFactorAuthentication()) {
                    $table->timestamp('two_factor_confirmed_at')
                        ->nullable();
                }

                $table->rememberToken();
                $table->timestamps(6);
                $table->softDeletes('deleted_at', 6);

                $table->unique('name');
            });
        }
    }
};
