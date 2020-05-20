<?php

use App\Enums\OverlapType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVideoOverlapColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('video', function (Blueprint $table) {
            $table->dropColumn('trans');
            $table->dropColumn('over');
            $table->integer('overlap')->default(OverlapType::NONE);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('video', function (Blueprint $table) {
            $table->boolean('trans')->default(false);
            $table->boolean('over')->default(false);
            $table->dropColumn('overlap');
        });
    }
}
