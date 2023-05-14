<?php

declare(strict_types=1);

use App\Models\Admin\Feature;
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
        if (! Schema::hasTable(Feature::TABLE)) {
            Schema::create(Feature::TABLE, function (Blueprint $table) {
                $table->id(Feature::ATTRIBUTE_ID);
                $table->string(Feature::ATTRIBUTE_NAME);
                $table->string(Feature::ATTRIBUTE_SCOPE);
                $table->text(Feature::ATTRIBUTE_VALUE);
                $table->timestamps(6);

                $table->unique([Feature::ATTRIBUTE_NAME, Feature::ATTRIBUTE_SCOPE]);
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
        Schema::dropIfExists(Feature::TABLE);
    }
};
