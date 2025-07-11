<?php

declare(strict_types=1);

use App\Enums\Models\List\ExternalProfileVisibility;
use App\Models\Auth\User;
use App\Models\List\ExternalProfile;
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
        if (! Schema::hasTable(ExternalProfile::TABLE)) {
            Schema::create(ExternalProfile::TABLE, function (Blueprint $table) {
                $table->timestamps(6);
                $table->id(ExternalProfile::ATTRIBUTE_ID);
                $table->string(ExternalProfile::ATTRIBUTE_NAME);
                $table->integer(ExternalProfile::ATTRIBUTE_SITE);
                $table->integer(ExternalProfile::ATTRIBUTE_VISIBILITY)->default(ExternalProfileVisibility::PRIVATE->value);

                $table->unsignedBigInteger(ExternalProfile::ATTRIBUTE_USER)->nullable();
                $table->foreign(ExternalProfile::ATTRIBUTE_USER)->references(User::ATTRIBUTE_ID)->on(User::TABLE)->cascadeOnDelete();

                $table->timestamp(ExternalProfile::ATTRIBUTE_SYNCED_AT, 6)->nullable();
                $table->integer(ExternalProfile::ATTRIBUTE_EXTERNAL_USER_ID)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(ExternalProfile::TABLE);
    }
};
