<?php

declare(strict_types=1);

use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioImage;
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
        if (! Schema::hasTable(StudioImage::TABLE)) {
            Schema::create(StudioImage::TABLE, function (Blueprint $table) {
                $table->timestamps(6);
                $table->unsignedBigInteger(StudioImage::ATTRIBUTE_STUDIO);
                $table->foreign(StudioImage::ATTRIBUTE_STUDIO)->references(Studio::ATTRIBUTE_ID)->on(Studio::TABLE)->cascadeOnDelete();
                $table->unsignedBigInteger(StudioImage::ATTRIBUTE_IMAGE);
                $table->foreign(StudioImage::ATTRIBUTE_IMAGE)->references(Image::ATTRIBUTE_ID)->on(Image::TABLE)->cascadeOnDelete();
                $table->primary([StudioImage::ATTRIBUTE_STUDIO, StudioImage::ATTRIBUTE_IMAGE]);
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
        Schema::dropIfExists(StudioImage::TABLE);
    }
};
