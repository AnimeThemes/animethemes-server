<?php

declare(strict_types=1);

use App\Models\BaseModel;
use App\Models\Wiki\Series;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateSeriesTable.
 */
class CreateSeriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Series::TABLE, function (Blueprint $table) {
            $table->id(Series::ATTRIBUTE_ID);
            $table->timestamps(6);
            $table->softDeletes(BaseModel::ATTRIBUTE_DELETED_AT, 6);
            $table->string(Series::ATTRIBUTE_SLUG);
            $table->string(Series::ATTRIBUTE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Series::TABLE);
    }
}
