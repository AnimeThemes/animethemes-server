<?php

declare(strict_types=1);

use App\Models\Auth\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The database schema.
     *
     * @var Builder
     */
    protected Builder $schema;

    /**
     * The table name.
     *
     * @var string
     */
    protected mixed $table;

    public function __construct()
    {
        $this->schema = Schema::connection(
            config('eloquent-viewable.models.view.connection')
        );

        $this->table = config('eloquent-viewable.models.view.table_name');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! $this->schema->hasTable($this->table)) {
            $this->schema->create($this->table, function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->morphs('viewable');
                $table->text('visitor')->nullable();
                $table->string('collection')->nullable();
                $table->timestamp('viewed_at')->useCurrent();

                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')->references(User::ATTRIBUTE_ID)->on(User::TABLE)->nullOnDelete();

                $table->string('referer', 1000)->nullable();
                $table->string('user_agent', 1000)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
};
