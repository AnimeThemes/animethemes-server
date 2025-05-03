<?php

declare(strict_types=1);

use App\Models\Auth\User;
use App\Models\User\Encode;
use App\Models\Wiki\Video;
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
        if (! Schema::hasTable(Encode::TABLE)) {
            Schema::create(Encode::TABLE, function (Blueprint $table) {
                $table->id(Encode::ATTRIBUTE_ID);
                $table->unsignedBigInteger(Encode::ATTRIBUTE_USER);
                $table->foreign(Encode::ATTRIBUTE_USER)->references(User::ATTRIBUTE_ID)->on(User::TABLE)->cascadeOnDelete();
                $table->unsignedBigInteger(Encode::ATTRIBUTE_VIDEO);
                $table->foreign(Encode::ATTRIBUTE_VIDEO)->references(Video::ATTRIBUTE_ID)->on(Video::TABLE)->cascadeOnDelete();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Encode::TABLE);
    }
};
