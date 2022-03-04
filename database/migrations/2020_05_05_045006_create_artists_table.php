<?php

declare(strict_types=1);

use App\Models\BaseModel;
use App\Models\Wiki\Artist;
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
        Schema::create(Artist::TABLE, function (Blueprint $table) {
            $table->id(Artist::ATTRIBUTE_ID);
            $table->timestamps(6);
            $table->softDeletes(BaseModel::ATTRIBUTE_DELETED_AT, 6);
            $table->string(Artist::ATTRIBUTE_SLUG);
            $table->string(Artist::ATTRIBUTE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(Artist::TABLE);
    }
};
