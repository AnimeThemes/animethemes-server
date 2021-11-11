<?php

declare(strict_types=1);

use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\StudioResource;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateStudioResource.
 */
class CreateStudioResource extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(StudioResource::TABLE, function (Blueprint $table) {
            $table->timestamps(6);
            $table->unsignedBigInteger(StudioResource::ATTRIBUTE_STUDIO);
            $table->foreign(StudioResource::ATTRIBUTE_STUDIO)->references(Studio::ATTRIBUTE_ID)->on(Studio::TABLE)->cascadeOnDelete();
            $table->unsignedBigInteger(StudioResource::ATTRIBUTE_RESOURCE);
            $table->foreign(StudioResource::ATTRIBUTE_RESOURCE)->references(ExternalResource::ATTRIBUTE_ID)->on(ExternalResource::TABLE)->cascadeOnDelete();
            $table->primary([StudioResource::ATTRIBUTE_STUDIO, StudioResource::ATTRIBUTE_RESOURCE]);
            $table->string(StudioResource::ATTRIBUTE_AS)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(StudioResource::TABLE);
    }
}
