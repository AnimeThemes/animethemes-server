<?php

declare(strict_types=1);

namespace App\Nova\Actions\Storage\Admin;

use App\Actions\Storage\Admin\Dump\DumpAction as DumpDatabase;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\PostgresConnection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class DatabaseDumpAction.
 */
abstract class DumpAction extends Action
{
    /**
     * Perform the action on the given models.
     *
     * @param  ActionFields  $fields
     * @param  Collection  $models
     * @return array
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function handle(ActionFields $fields, Collection $models): array
    {
        $action = $this->action($fields);

        $result = $action->handle();

        $result->toLog();

        if ($result->hasFailed()) {
            return Action::danger($result->getMessage());
        }

        return Action::message($result->getMessage());
    }

    /**
     * Get the fields available on the action.
     *
     * @param  NovaRequest  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function fields(NovaRequest $request): array
    {
        $connection = DB::connection();

        return match (get_class($connection)) {
            MySqlConnection::class => $this->fieldsForMySql(),
            PostgresConnection::class => $this->fieldsForPostgreSql(),
            default => [],
        };
    }

    /**
     * Get the fields available on the action for a MySql db connection.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function fieldsForMySql(): array
    {
        return [
            Boolean::make(__('nova.actions.dump.dump.fields.mysql.comments.name'), 'comments')
                ->help(__('nova.actions.dump.dump.fields.mysql.comments.help')),

            Boolean::make(__('nova.actions.dump.dump.fields.mysql.skip_comments.name'), 'skip-comments')
                ->help(__('nova.actions.dump.dump.fields.mysql.skip_comments.help')),

            Boolean::make(__('nova.actions.dump.dump.fields.mysql.extended_insert.name'), 'extended-insert')
                ->help(__('nova.actions.dump.dump.fields.mysql.extended_insert.help')),

            Boolean::make(__('nova.actions.dump.dump.fields.mysql.skip_extended_insert.name'), 'skip-extended-insert')
                ->help(__('nova.actions.dump.dump.fields.mysql.skip_extended_insert.help')),

            Boolean::make(__('nova.actions.dump.dump.fields.mysql.single_transaction.name'), 'single-transaction')
                ->help(__('nova.actions.dump.dump.fields.mysql.single_transaction.help')),

            Boolean::make(__('nova.actions.dump.dump.fields.mysql.lock_tables.name'), 'lock-tables')
                ->help(__('nova.actions.dump.dump.fields.mysql.lock_tables.help')),

            Boolean::make(__('nova.actions.dump.dump.fields.mysql.skip_lock_tables.name'), 'skip-lock-tables')
                ->help(__('nova.actions.dump.dump.fields.mysql.skip_lock_tables.help')),

            Boolean::make(__('nova.actions.dump.dump.fields.mysql.skip_column_statistics.name'), 'skip-column-statistics')
                ->help(__('nova.actions.dump.dump.fields.mysql.skip_column_statistics.help')),

            Boolean::make(__('nova.actions.dump.dump.fields.mysql.quick.name'), 'quick')
                ->help(__('nova.actions.dump.dump.fields.mysql.quick.help')),

            Boolean::make(__('nova.actions.dump.dump.fields.mysql.skip_quick.name'), 'skip-quick')
                ->help(__('nova.actions.dump.dump.fields.mysql.skip_quick.help')),

            Text::make(__('nova.actions.dump.dump.fields.mysql.default_character_set.name'), 'default-character-set')
                ->nullable()
                ->rules(['nullable', 'string', 'max:192'])
                ->help(__('nova.actions.dump.dump.fields.mysql.default_character_set.help')),

            Select::make(__('nova.actions.dump.dump.fields.mysql.set_gtid_purged.name'), 'set-gtid-purged')
                ->options([
                    'OFF' => __('nova.actions.dump.dump.fields.mysql.set_gtid_purged.options.off'),
                    'ON' => __('nova.actions.dump.dump.fields.mysql.set_gtid_purged.options.on'),
                    'AUTO' => __('nova.actions.dump.dump.fields.mysql.set_gtid_purged.options.auto'),
                ])
                ->nullable()
                ->rules(['nullable', Rule::in(['OFF', 'ON', 'AUTO'])->__toString()])
                ->help(__('nova.actions.dump.dump.fields.mysql.set_gtid_purged.help')),

            Boolean::make(__('nova.actions.dump.dump.fields.mysql.no_create_info.name'), 'no-create-info')
                ->help(__('nova.actions.dump.dump.fields.mysql.no_create_info.help')),
        ];
    }

    /**
     * Get the fields available on the action for a PostgreSql db connection.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function fieldsForPostgreSql(): array
    {
        return [
            Boolean::make(__('nova.actions.dump.dump.fields.postgresql.inserts.name'), 'inserts')
                ->help(__('nova.actions.dump.dump.fields.postgresql.inserts.help')),

            Boolean::make(__('nova.actions.dump.dump.fields.postgresql.data_only.name'), 'data-only')
                ->help(__('nova.actions.dump.dump.fields.postgresql.data_only.help')),
        ];
    }

    /**
     * Get the underlying action.
     *
     * @param  ActionFields  $fields
     * @return DumpDatabase
     */
    abstract protected function action(ActionFields $fields): DumpDatabase;
}
