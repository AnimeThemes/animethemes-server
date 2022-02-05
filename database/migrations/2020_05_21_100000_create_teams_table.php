<?php

declare(strict_types=1);

use App\Models\Auth\Team;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateTeamsTable.
 */
class CreateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(Team::TABLE, function (Blueprint $table) {
            $table->id();
            $table->foreignId(Team::ATTRIBUTE_USER)->index();
            $table->string(Team::ATTRIBUTE_NAME);
            $table->boolean(Team::ATTRIBUTE_PERSONAL_TEAM);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(Team::TABLE);
    }
}
