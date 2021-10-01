<?php

declare(strict_types=1);

use App\Models\Admin\Announcement;
use App\Models\BaseModel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateAnnouncementsTable.
 */
class CreateAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Announcement::TABLE, function (Blueprint $table) {
            $table->id(Announcement::ATTRIBUTE_ID);
            $table->timestamps(6);
            $table->softDeletes(BaseModel::ATTRIBUTE_DELETED_AT, 6);
            $table->text(Announcement::ATTRIBUTE_CONTENT);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Announcement::TABLE);
    }
}
