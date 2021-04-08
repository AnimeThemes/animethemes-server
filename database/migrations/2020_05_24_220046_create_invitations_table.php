<?php

use App\Enums\InvitationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invitation', function (Blueprint $table) {
            $table->id('invitation_id');
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);
            $table->string('token');
            $table->string('name');
            $table->string('email');
            $table->integer('status')->default(InvitationStatus::OPEN);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invitation');
    }
}
