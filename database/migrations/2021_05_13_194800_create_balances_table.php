<?php

declare(strict_types=1);

use App\Models\BaseModel;
use App\Models\Billing\Balance;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateBalancesTable.
 */
class CreateBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(Balance::TABLE, function (Blueprint $table) {
            $table->id(Balance::ATTRIBUTE_ID);
            $table->timestamps(6);
            $table->softDeletes(BaseModel::ATTRIBUTE_DELETED_AT, 6);
            $table->date(Balance::ATTRIBUTE_DATE);
            $table->integer(Balance::ATTRIBUTE_SERVICE);
            $table->integer(Balance::ATTRIBUTE_FREQUENCY);
            $table->decimal(Balance::ATTRIBUTE_USAGE);
            $table->decimal(Balance::ATTRIBUTE_BALANCE);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(Balance::TABLE);
    }
}
