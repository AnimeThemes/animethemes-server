<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Class UpdateTelescopeEntriesTable
 */
class UpdateTelescopeEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('telescope_entries') && DB::connection()->getName() === 'mysql') {
            Schema::table('telescope_entries', function (Blueprint $table) {
                $table->json('content')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('telescope_entries') && DB::connection()->getName() === 'mysql') {
            Schema::table('telescope_entries', function (Blueprint $table) {
                $table->longText('content')->change();
            });
        }
    }
}
