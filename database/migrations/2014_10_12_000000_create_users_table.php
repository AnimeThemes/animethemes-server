<?php

declare(strict_types=1);

use App\Models\Auth\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateUsersTable.
 */
class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(User::TABLE, function (Blueprint $table) {
            $table->id();
            $table->string(User::ATTRIBUTE_NAME);
            $table->string(User::ATTRIBUTE_EMAIL)->unique();
            $table->timestamp(User::ATTRIBUTE_EMAIL_VERIFIED_AT)->nullable();
            $table->string(User::ATTRIBUTE_PASSWORD);
            $table->rememberToken();
            $table->string(User::ATTRIBUTE_CURRENT_TEAM)->nullable();
            $table->timestamps(6);
            $table->softDeletes(User::ATTRIBUTE_DELETED_AT, 6);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(User::TABLE);
    }
}
