<?php

declare(strict_types=1);

use App\Models\BaseModel;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateResourcesTable.
 */
class CreateResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(ExternalResource::TABLE, function (Blueprint $table) {
            $table->id(ExternalResource::ATTRIBUTE_ID);
            $table->timestamps(6);
            $table->softDeletes(BaseModel::ATTRIBUTE_DELETED_AT, 6);
            $table->integer(ExternalResource::ATTRIBUTE_SITE)->nullable();
            $table->string(ExternalResource::ATTRIBUTE_LINK)->nullable();
            $table->integer(ExternalResource::ATTRIBUTE_EXTERNAL_ID)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(ExternalResource::TABLE);
    }
}
