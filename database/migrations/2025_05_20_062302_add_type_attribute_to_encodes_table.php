<?php

declare(strict_types=1);

use App\Models\User\Encode;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn(Encode::TABLE, Encode::ATTRIBUTE_TYPE)) {
            Schema::table(Encode::TABLE, function (Blueprint $table) {
                $table->integer(Encode::ATTRIBUTE_TYPE)->after(Encode::ATTRIBUTE_VIDEO);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn(Encode::TABLE, Encode::ATTRIBUTE_TYPE)) {
            Schema::table(Encode::TABLE, function (Blueprint $table) {
                $table->dropColumn(Encode::ATTRIBUTE_TYPE);
            });
        }
    }
};
