<?php

declare(strict_types=1);

use App\Models\Wiki\Image;
use App\Pivots\Morph\Imageable;
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
        if (! Schema::hasTable(Imageable::TABLE)) {
            Schema::create(Imageable::TABLE, function (Blueprint $table) {
                $table->unsignedBigInteger(Imageable::ATTRIBUTE_IMAGE);
                $table->foreign(Imageable::ATTRIBUTE_IMAGE)->references(Image::ATTRIBUTE_ID)->on(Image::TABLE)->cascadeOnDelete();

                $table->morphs(Imageable::RELATION_IMAGEABLE);
                $table->integer(Imageable::ATTRIBUTE_DEPTH)->nullable();

                $table->timestamps(6);

                $table->primary([
                    Imageable::ATTRIBUTE_IMAGE,
                    Imageable::ATTRIBUTE_IMAGEABLE_TYPE,
                    Imageable::ATTRIBUTE_IMAGEABLE_ID,
                ]);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Imageable::TABLE);
    }
};
