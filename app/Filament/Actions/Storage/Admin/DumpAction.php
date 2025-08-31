<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Admin;

use App\Actions\Storage\Admin\Dump\DumpAction as DumpDatabase;
use App\Filament\Actions\BaseAction;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Fields\TextInput;
use App\Models\Admin\Dump;
use Exception;
use Filament\Forms\Components\Checkbox;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\PostgresConnection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

abstract class DumpAction extends BaseAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(Heroicon::OutlinedCircleStack);

        $this->visible(Gate::allows('create', Dump::class));

        $this->action(fn (array $data) => $this->handle($data));
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws Exception
     */
    public function handle(array $data): void
    {
        $action = $this->storageAction($data);

        $result = $action->handle();

        $result->toLog();
    }

    public function getSchema(Schema $schema): Schema
    {
        $connection = DB::connection();

        return match (get_class($connection)) {
            MySqlConnection::class => $this->fieldsForMySql($schema),
            PostgresConnection::class => $this->fieldsForPostgreSql($schema),
            default => $schema,
        };
    }

    public function fieldsForMySql(Schema $schema): Schema
    {
        return $schema
            ->components([
                Checkbox::make('comments')
                    ->label(__('filament.actions.dump.dump.fields.mysql.comments.name'))
                    ->helperText(__('filament.actions.dump.dump.fields.mysql.comments.help')),

                Checkbox::make('skip-comments')
                    ->label(__('filament.actions.dump.dump.fields.mysql.skip_comments.name'))
                    ->helperText(__('filament.actions.dump.dump.fields.mysql.skip_comments.help')),

                Checkbox::make('extended-insert')
                    ->label(__('filament.actions.dump.dump.fields.mysql.extended_insert.name'))
                    ->helperText(__('filament.actions.dump.dump.fields.mysql.extended_insert.help')),

                Checkbox::make('skip-extended-insert')
                    ->label(__('filament.actions.dump.dump.fields.mysql.skip_extended_insert.name'))
                    ->helperText(__('filament.actions.dump.dump.fields.mysql.skip_extended_insert.help')),

                Checkbox::make('single-transaction')
                    ->label(__('filament.actions.dump.dump.fields.mysql.single_transaction.name'))
                    ->helperText(__('filament.actions.dump.dump.fields.mysql.single_transaction.help')),

                Checkbox::make('lock-tables')
                    ->label(__('filament.actions.dump.dump.fields.mysql.lock_tables.name'))
                    ->helperText(__('filament.actions.dump.dump.fields.mysql.lock_tables.help')),

                Checkbox::make('skip-lock-tables')
                    ->label(__('filament.actions.dump.dump.fields.mysql.skip_lock_tables.name'))
                    ->helperText(__('filament.actions.dump.dump.fields.mysql.skip_lock_tables.help')),

                Checkbox::make('skip-column-statistics')
                    ->label(__('filament.actions.dump.dump.fields.mysql.skip_column_statistics.name'))
                    ->helperText(__('filament.actions.dump.dump.fields.mysql.skip_column_statistics.help')),

                Checkbox::make('quick')
                    ->label(__('filament.actions.dump.dump.fields.mysql.quick.name'))
                    ->helperText(__('filament.actions.dump.dump.fields.mysql.quick.help')),

                Checkbox::make('skip-quick')
                    ->label(__('filament.actions.dump.dump.fields.mysql.skip_quick.name'))
                    ->helperText(__('filament.actions.dump.dump.fields.mysql.skip_quick.help')),

                TextInput::make('default-character-set')
                    ->label(__('filament.actions.dump.dump.fields.mysql.default_character_set.name'))
                    ->helperText(__('filament.actions.dump.dump.fields.mysql.default_character_set.help'))
                    ->maxLength(192),

                Select::make('set-gtid-purged')
                    ->label(__('filament.actions.dump.dump.fields.mysql.set_gtid_purged.name'))
                    ->helperText(__('filament.actions.dump.dump.fields.mysql.set_gtid_purged.help'))
                    ->options([
                        'OFF' => __('filament.actions.dump.dump.fields.mysql.set_gtid_purged.options.off'),
                        'ON' => __('filament.actions.dump.dump.fields.mysql.set_gtid_purged.options.on'),
                        'AUTO' => __('filament.actions.dump.dump.fields.mysql.set_gtid_purged.options.auto'),
                    ])
                    ->rule(Rule::in(['OFF', 'ON', 'AUTO'])->__toString()),

                Checkbox::make('no-create-info')
                    ->label(__('filament.actions.dump.dump.fields.mysql.no_create_info.name'))
                    ->helperText(__('filament.actions.dump.dump.fields.mysql.no_create_info.help')),
            ]);
    }

    public function fieldsForPostgreSql(Schema $schema): Schema
    {
        return $schema
            ->components([
                Checkbox::make('inserts')
                    ->label(__('filament.actions.dump.dump.fields.postgresql.inserts.name'))
                    ->helperText(__('filament.actions.dump.dump.fields.postgresql.inserts.help')),

                Checkbox::make('data-only')
                    ->label(__('filament.actions.dump.dump.fields.postgresql.data_only.name'))
                    ->helperText(__('filament.actions.dump.dump.fields.postgresql.data_only.help')),
            ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    abstract protected function storageAction(array $data): DumpDatabase;
}
