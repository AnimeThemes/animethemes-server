<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (! Schema::hasTable(Config::get('setting.database.table'))) {
            Schema::create(Config::get('setting.database.table'), function (Blueprint $table) {
                $table->increments('id');
                $table->string(Config::get('setting.database.key'))->index();
                $table->text(Config::get('setting.database.value'));
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(Config::get('setting.database.table'));
    }
};
