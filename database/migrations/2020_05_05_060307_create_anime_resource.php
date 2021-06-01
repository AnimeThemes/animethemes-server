<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateAnimeResource
 */
class CreateAnimeResource extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anime_resource', function (Blueprint $table) {
            $table->timestamps(6);
            $table->unsignedBigInteger('anime_id');
            $table->foreign('anime_id')->references('anime_id')->on('anime')->onDelete('cascade');
            $table->unsignedBigInteger('resource_id');
            $table->foreign('resource_id')->references('resource_id')->on('resource')->onDelete('cascade');
            $table->primary(['anime_id', 'resource_id']);
            $table->string('as')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('anime_resource');
    }
}
