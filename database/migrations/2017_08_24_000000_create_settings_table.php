<?php

declare(strict_types=1);

use App\Models\Admin\Setting;
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
        if (! Schema::hasTable(Setting::TABLE)) {
            Schema::create(Setting::TABLE, function (Blueprint $table) {
                $table->id();
                $table->string(Setting::ATTRIBUTE_KEY)->index();
                $table->text(Setting::ATTRIBUTE_VALUE);
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
        Schema::dropIfExists(Setting::TABLE);
    }
};
