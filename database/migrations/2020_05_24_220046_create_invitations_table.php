<?php

declare(strict_types=1);

use App\Enums\Models\Auth\InvitationStatus;
use App\Models\Auth\Invitation;
use App\Models\BaseModel;
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
        if (! Schema::hasTable(Invitation::TABLE)) {
            Schema::create(Invitation::TABLE, function (Blueprint $table) {
                $table->id(Invitation::ATTRIBUTE_ID);
                $table->timestamps(6);
                $table->softDeletes(BaseModel::ATTRIBUTE_DELETED_AT, 6);
                $table->string(Invitation::ATTRIBUTE_NAME);
                $table->string(Invitation::ATTRIBUTE_EMAIL);
                $table->integer(Invitation::ATTRIBUTE_STATUS)->default(InvitationStatus::OPEN);
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
        Schema::dropIfExists(Invitation::TABLE);
    }
};
