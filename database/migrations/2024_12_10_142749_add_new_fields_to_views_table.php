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

    /**
     * Create a new migration instance.
     *
     * @return void
     */
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
        if (!$this->schema->hasColumn($this->table, 'user_id')) {
            $this->schema->table($this->table, function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')->references(User::ATTRIBUTE_ID)->on(User::TABLE);
            });
        }

        if (!$this->schema->hasColumn($this->table, 'referer')) {
            $this->schema->table($this->table, function (Blueprint $table) {
                $table->string('referer')->nullable();
            });
        }

        if (!$this->schema->hasColumn($this->table, 'user_agent')) {
            $this->schema->table($this->table, function (Blueprint $table) {
                $table->string('user_agent')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ($this->schema->hasColumn($this->table, 'user_id')) {
            $this->schema->table($this->table, function (Blueprint $table) {
                $table->dropConstrainedForeignId('user_id');
            });
        }

        if ($this->schema->hasColumn($this->table, 'referer')) {
            $this->schema->table($this->table, function (Blueprint $table) {
                $table->dropColumn('referer');
            });
        }

        if ($this->schema->hasColumn($this->table, 'user_agent')) {
            $this->schema->table($this->table, function (Blueprint $table) {
                $table->dropColumn('user_agent');
            });
        }
    }
};
