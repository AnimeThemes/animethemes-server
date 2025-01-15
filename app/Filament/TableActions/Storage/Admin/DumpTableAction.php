<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Storage\Admin;

use App\Actions\Storage\Admin\Dump\DumpAction as DumpDatabase;
use App\Filament\Components\Fields\Select;
use App\Filament\TableActions\BaseTableAction;
use App\Models\Admin\Dump;
use Exception;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\PostgresConnection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Class DumpTableAction.
 */
abstract class DumpTableAction extends BaseTableAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(__('filament-icons.table_actions.dump.dump'));

        $this->authorize('create', Dump::class);
    }

    /**
     * Perform the action on the given models.
     *
     * @param  array  $fields
     * @return void
     *
     * @throws Exception
     */
    public function handle(array $fields): void
    {
        $action = $this->storageAction($fields);

        $result = $action->handle();

        $result->toLog();
    }

    /**
     * Get the fields available on the action.
     *
     * @param  Form  $form
     * @return Form
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getForm(Form $form): Form
    {
        $connection = DB::connection();

        return match (get_class($connection)) {
            MySqlConnection::class => $this->fieldsForMySql($form),
            PostgresConnection::class => $this->fieldsForPostgreSql($form),
            default => $form,
        };
    }

    /**
     * Get the fields available on the action for a MySql db connection.
     *
     * @param  Form  $form
     * @return Form
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function fieldsForMySql(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->nullable()
                    ->rules(['nullable', 'string', 'max:192']),

                Select::make('set-gtid-purged')
                    ->label(__('filament.actions.dump.dump.fields.mysql.set_gtid_purged.name'))
                    ->helperText(__('filament.actions.dump.dump.fields.mysql.set_gtid_purged.help'))
                    ->options([
                        'OFF' => __('filament.actions.dump.dump.fields.mysql.set_gtid_purged.options.off'),
                        'ON' => __('filament.actions.dump.dump.fields.mysql.set_gtid_purged.options.on'),
                        'AUTO' => __('filament.actions.dump.dump.fields.mysql.set_gtid_purged.options.auto'),
                    ])
                    ->nullable()
                    ->rules(['nullable', Rule::in(['OFF', 'ON', 'AUTO'])->__toString()]),

                Checkbox::make('no-create-info')
                    ->label(__('filament.actions.dump.dump.fields.mysql.no_create_info.name'))
                    ->helperText(__('filament.actions.dump.dump.fields.mysql.no_create_info.help')),
            ]);
    }

    /**
     * Get the fields available on the action for a PostgreSql db connection.
     *
     * @param  Form  $form
     * @return Form
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function fieldsForPostgreSql(Form $form): Form
    {
        return $form
            ->schema([
                Checkbox::make('inserts')
                    ->label(__('filament.actions.dump.dump.fields.postgresql.inserts.name'))
                    ->helperText(__('filament.actions.dump.dump.fields.postgresql.inserts.help')),

                Checkbox::make('data-only')
                    ->label(__('filament.actions.dump.dump.fields.postgresql.data_only.name'))
                    ->helperText(__('filament.actions.dump.dump.fields.postgresql.data_only.help')),
            ]);
    }

    /**
     * Get the underlying action.
     *
     * @param  array  $fields
     * @return DumpDatabase
     */
    abstract protected function storageAction(array $fields): DumpDatabase;
}
