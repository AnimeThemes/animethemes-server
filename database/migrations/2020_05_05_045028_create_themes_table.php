<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateThemesTable
 */
class CreateThemesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('theme', function (Blueprint $table) {
            $table->id('theme_id');
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);
            $table->string('group')->nullable();
            $table->integer('type')->nullable();
            $table->integer('sequence')->nullable();
            $table->string('slug');

            $table->unsignedBigInteger('anime_id');
            $table->foreign('anime_id')->references('anime_id')->on('anime')->onDelete('cascade');

            $table->unsignedBigInteger('song_id')->nullable();
            $table->foreign('song_id')->references('song_id')->on('song')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('theme');
    }
}
