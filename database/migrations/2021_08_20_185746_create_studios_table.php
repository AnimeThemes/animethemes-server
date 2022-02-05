<?php

declare(strict_types=1);

use App\Models\BaseModel;
use App\Models\Wiki\Studio;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateStudiosTable.
 */
class CreateStudiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(Studio::TABLE, function (Blueprint $table) {
            $table->id(Studio::ATTRIBUTE_ID);
            $table->timestamps(6);
            $table->softDeletes(BaseModel::ATTRIBUTE_DELETED_AT, 6);
            $table->string(Studio::ATTRIBUTE_SLUG);
            $table->string(Studio::ATTRIBUTE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(Studio::TABLE);
    }
}
