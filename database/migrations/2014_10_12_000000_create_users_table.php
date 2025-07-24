<?php

declare(strict_types=1);

use App\Models\Auth\User;
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
        if (! Schema::hasTable(User::TABLE)) {
            Schema::create(User::TABLE, function (Blueprint $table) {
                $table->id();
                $table->string(User::ATTRIBUTE_NAME);
                $table->string(User::ATTRIBUTE_EMAIL)->unique();
                $table->timestamp(User::ATTRIBUTE_EMAIL_VERIFIED_AT)->nullable();
                $table->string(User::ATTRIBUTE_PASSWORD);

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
                $table->softDeletes(User::ATTRIBUTE_DELETED_AT, 6);

                $table->unique(User::ATTRIBUTE_NAME);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(User::TABLE);
    }
};
