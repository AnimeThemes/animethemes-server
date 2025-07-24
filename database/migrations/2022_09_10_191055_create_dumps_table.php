<?php

declare(strict_types=1);

use App\Models\Admin\Dump;
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
        if (! Schema::hasTable(Dump::TABLE)) {
            Schema::create(Dump::TABLE, function (Blueprint $table) {
                $table->id(Dump::ATTRIBUTE_ID);
                $table->timestamps(6);
                $table->string(Dump::ATTRIBUTE_PATH);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Dump::TABLE);
    }
};
