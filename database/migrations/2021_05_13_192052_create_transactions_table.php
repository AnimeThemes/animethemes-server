<?php

declare(strict_types=1);

use App\Models\BaseModel;
use App\Models\Billing\Transaction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::create(Transaction::TABLE, function (Blueprint $table) {
            $table->id(Transaction::ATTRIBUTE_ID);
            $table->timestamps(6);
            $table->softDeletes(BaseModel::ATTRIBUTE_DELETED_AT, 6);
            $table->date(Transaction::ATTRIBUTE_DATE);
            $table->integer(Transaction::ATTRIBUTE_SERVICE);
            $table->string(Transaction::ATTRIBUTE_DESCRIPTION);
            $table->decimal(Transaction::ATTRIBUTE_AMOUNT);
            $table->string(Transaction::ATTRIBUTE_EXTERNAL_ID)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(Transaction::TABLE);
    }
};
