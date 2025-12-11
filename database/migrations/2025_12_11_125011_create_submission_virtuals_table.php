<?php

declare(strict_types=1);

use App\Models\Auth\User;
use App\Models\User\Submission\SubmissionVirtual;
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
        if (! Schema::hasTable(SubmissionVirtual::TABLE)) {
            Schema::create(SubmissionVirtual::TABLE, function (Blueprint $table) {
                $table->id(SubmissionVirtual::ATTRIBUTE_ID);

                $table->boolean(SubmissionVirtual::ATTRIBUTE_EXISTS)->default(false);
                $table->string(SubmissionVirtual::ATTRIBUTE_MODEL_TYPE);
                $table->json(SubmissionVirtual::ATTRIBUTE_FIELDS);

                $table->unsignedBigInteger(SubmissionVirtual::ATTRIBUTE_USER)->nullable();
                $table->foreign(SubmissionVirtual::ATTRIBUTE_USER)->references(User::ATTRIBUTE_ID)->on(User::TABLE)->nullOnDelete();

                $table->timestamps(6);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(SubmissionVirtual::TABLE);
    }
};
