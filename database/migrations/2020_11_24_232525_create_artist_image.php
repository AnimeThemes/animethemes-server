<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateArtistImage
 */
class CreateArtistImage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('artist_image', function (Blueprint $table) {
            $table->timestamps(6);
            $table->unsignedBigInteger('artist_id');
            $table->foreign('artist_id')->references('artist_id')->on('artist')->onDelete('cascade');
            $table->unsignedBigInteger('image_id');
            $table->foreign('image_id')->references('image_id')->on('image')->onDelete('cascade');
            $table->primary(['artist_id', 'image_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('artist_image');
    }
}
