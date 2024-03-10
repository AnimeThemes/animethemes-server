<?php

declare(strict_types=1);

use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\List\UserExternalProfile;
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
        if (! Schema::hasTable(UserExternalProfile::TABLE)) {
            Schema::create(UserExternalProfile::TABLE, function (Blueprint $table) {
                $table->timestamps(6);
                $table->softDeletes(BaseModel::ATTRIBUTE_DELETED_AT, 6);
                $table->id(UserExternalProfile::ATTRIBUTE_ID);
                $table->string(UserExternalProfile::ATTRIBUTE_USERNAME);
                $table->integer(UserExternalProfile::ATTRIBUTE_SITE)->nullable();

                $table->unsignedBigInteger(UserExternalProfile::ATTRIBUTE_USER);
                $table->foreign(UserExternalProfile::ATTRIBUTE_USER)->references(User::ATTRIBUTE_ID)->on(User::TABLE)->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(UserExternalProfile::TABLE);
    }
};
