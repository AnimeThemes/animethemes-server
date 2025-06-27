<?php

declare(strict_types=1);

use App\Models\Auth\User;
use App\Models\User\Like;
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
        if (! Schema::hasTable(Like::TABLE)) {
            Schema::create(Like::TABLE, function (Blueprint $table) {
                $table->id(Like::ATTRIBUTE_ID);
                $table->unsignedBigInteger(Like::ATTRIBUTE_USER)->nullable();
                $table->foreign(Like::ATTRIBUTE_USER)->references(User::ATTRIBUTE_ID)->on(User::TABLE)->cascadeOnDelete();

                $table->morphs(Like::ATTRIBUTE_LIKEABLE);
                $table->timestamps(6);

                $table->index([Like::ATTRIBUTE_USER, Like::ATTRIBUTE_LIKEABLE_TYPE, Like::ATTRIBUTE_LIKEABLE_ID]);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Like::TABLE);
    }
};
