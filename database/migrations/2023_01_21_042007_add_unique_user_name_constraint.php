<?php

declare(strict_types=1);

use App\Models\Auth\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (Schema::hasTable(User::TABLE)) {
            Schema::table(User::TABLE, function (Blueprint $table) {
                $table->unique(User::ATTRIBUTE_NAME);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasTable(User::TABLE)) {
            Schema::table(User::TABLE, function (Blueprint $table) {
                $table->dropUnique(User::ATTRIBUTE_NAME);
            });
        }
    }
};
