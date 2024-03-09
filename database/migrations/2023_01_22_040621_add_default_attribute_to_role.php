<?php

declare(strict_types=1);

use App\Models\Auth\Role;
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
        if (! Schema::hasColumn(Role::TABLE, Role::ATTRIBUTE_DEFAULT)) {
            Schema::table(Role::TABLE, function (Blueprint $table) {
                $table->boolean(Role::ATTRIBUTE_DEFAULT)->default(false);
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
        if (Schema::hasColumn(Role::TABLE, Role::ATTRIBUTE_DEFAULT)) {
            Schema::table(Role::TABLE, function (Blueprint $table) {
                $table->dropColumn(Role::ATTRIBUTE_DEFAULT);
            });
        }
    }
};
