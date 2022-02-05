<?php

declare(strict_types=1);

use App\Models\BaseModel;
use App\Models\Wiki\Image;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateImageTable.
 */
class CreateImageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(Image::TABLE, function (Blueprint $table) {
            $table->id(Image::ATTRIBUTE_ID);
            $table->timestamps(6);
            $table->softDeletes(BaseModel::ATTRIBUTE_DELETED_AT, 6);
            $table->string(Image::ATTRIBUTE_PATH);
            $table->integer(Image::ATTRIBUTE_SIZE);
            $table->string(Image::ATTRIBUTE_MIMETYPE);
            $table->integer(Image::ATTRIBUTE_FACET)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(Image::TABLE);
    }
}
