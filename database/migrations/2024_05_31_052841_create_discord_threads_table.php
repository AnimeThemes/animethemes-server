<?php

declare(strict_types=1);

use App\Models\BaseModel;
use App\Models\Discord\DiscordThread;
use App\Models\Wiki\Anime;
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
        if (! Schema::hasTable(DiscordThread::TABLE)) {
            Schema::create(DiscordThread::TABLE, function (Blueprint $table) {
                $table->timestamps(6);
                $table->softDeletes(BaseModel::ATTRIBUTE_DELETED_AT, 6);
                $table->bigInteger(DiscordThread::ATTRIBUTE_ID)->primary();
                $table->string(DiscordThread::ATTRIBUTE_NAME);

                $table->unsignedBigInteger(DiscordThread::ATTRIBUTE_ANIME);
                $table->foreign(DiscordThread::ATTRIBUTE_ANIME)->references(Anime::ATTRIBUTE_ID)->on(Anime::TABLE)->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DiscordThread::TABLE);
    }
};
