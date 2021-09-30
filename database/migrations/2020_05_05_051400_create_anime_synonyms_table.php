<?php

declare(strict_types=1);

use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateSynonymsTable.
 */
class CreateAnimeSynonymsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(AnimeSynonym::TABLE, function (Blueprint $table) {
            $table->id(AnimeSynonym::ATTRIBUTE_ID);
            $table->timestamps(6);
            $table->softDeletes(BaseModel::ATTRIBUTE_DELETED_AT, 6);
            $table->string(AnimeSynonym::ATTRIBUTE_TEXT)->nullable();

            $table->unsignedBigInteger(AnimeSynonym::ATTRIBUTE_ANIME);
            $table->foreign(AnimeSynonym::ATTRIBUTE_ANIME)->references(Anime::ATTRIBUTE_ID)->on(Anime::TABLE)->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(AnimeSynonym::TABLE);
    }
}
