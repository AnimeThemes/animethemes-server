<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateAnimeTable
 */
class CreateAnimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anime', function (Blueprint $table) {
            $table->id('anime_id');
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);
            $table->string('slug');
            $table->string('name');
            $table->integer('year')->nullable();
            $table->integer('season')->nullable();
            $table->text('synopsis')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('anime');
    }
}
