<?php

declare(strict_types=1);

use App\Models\BaseModel;
use App\Models\Wiki\Group;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable(Group::TABLE)) {
            Schema::create(Group::TABLE, function (Blueprint $table) {
                $table->id(Group::ATTRIBUTE_ID);
                $table->timestamps(6);
                $table->softDeletes(BaseModel::ATTRIBUTE_DELETED_AT, 6);
                $table->string(Group::ATTRIBUTE_NAME);
                $table->string(Group::ATTRIBUTE_SLUG);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Group::TABLE);
    }
};
