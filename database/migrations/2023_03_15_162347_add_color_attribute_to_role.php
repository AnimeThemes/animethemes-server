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
        Schema::table(Role::TABLE, function (Blueprint $table) {
            $table->string(Role::ATTRIBUTE_COLOR)->nullable();
            $table->integer(Role::ATTRIBUTE_PRIORITY)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table(Role::TABLE, function (Blueprint $table) {
            $table->dropColumn(Role::ATTRIBUTE_COLOR);
            $table->dropColumn(Role::ATTRIBUTE_PRIORITY);
        });
    }
};
