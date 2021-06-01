<?php declare(strict_types=1);

use App\Enums\VideoOverlap;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateVideosTable
 */
class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video', function (Blueprint $table) {
            $table->id('video_id');
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);
            $table->string('basename');
            $table->string('filename');
            $table->string('path');
            $table->integer('size');
            $table->string('mimetype');
            $table->integer('resolution')->nullable();
            $table->boolean('nc')->default(false);
            $table->boolean('subbed')->default(false);
            $table->boolean('lyrics')->default(false);
            $table->boolean('uncen')->default(false);
            $table->integer('overlap')->default(VideoOverlap::NONE);
            $table->integer('source')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video');
    }
}
