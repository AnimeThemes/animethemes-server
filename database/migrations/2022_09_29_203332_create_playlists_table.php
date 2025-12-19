<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('playlists')) {
            Schema::create('playlists', function (Blueprint $table) {
                $table->id('playlist_id');
                $table->timestamps(6);
                $hashIdColumn = $table->string('hashid')->nullable();
                if (DB::connection() instanceof MySqlConnection) {
                    // Set collation to binary to be case-sensitive
                    $hashIdColumn->collation('utf8mb4_bin');
                }
                $table->string('name');
                $table->integer('visibility');

                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

                $table->text('description')->nullable();
            });
        }
    }
};
