<?php

declare(strict_types=1);

use App\Models\BaseModel;
use App\Models\Wiki\Anime;
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
        Schema::create(Anime::TABLE, function (Blueprint $table) {
            $table->id(Anime::ATTRIBUTE_ID);
            $table->timestamps(6);
            $table->softDeletes(BaseModel::ATTRIBUTE_DELETED_AT, 6);
            $table->string(Anime::ATTRIBUTE_SLUG);
            $table->string(Anime::ATTRIBUTE_NAME);
            $table->integer(Anime::ATTRIBUTE_YEAR)->nullable();
            $table->integer(Anime::ATTRIBUTE_SEASON)->nullable();
            $table->text(Anime::ATTRIBUTE_SYNOPSIS)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(Anime::TABLE);
    }
};
