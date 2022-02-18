<?php

declare(strict_types=1);

use App\Models\BaseModel;
use App\Models\Document\Page;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreatePageTable.
 */
class CreatePageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(Page::TABLE, function (Blueprint $table) {
            $table->id(Page::ATTRIBUTE_ID);
            $table->timestamps(6);
            $table->softDeletes(BaseModel::ATTRIBUTE_DELETED_AT, 6);
            $table->string(Page::ATTRIBUTE_SLUG);
            $table->string(Page::ATTRIBUTE_NAME);
            $table->mediumText(Page::ATTRIBUTE_BODY);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(Page::TABLE);
    }
}
