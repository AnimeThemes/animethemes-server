<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Concerns\Filament\ActionLogs\HasActionLogs;
use App\Concerns\Filament\ActionLogs\HasPivotActionLogs;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Base\BaseManageResources;
use App\Filament\Resources\Base\BaseViewResource;
use App\Models\Admin\ActionLog;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

class EditAction extends Action
{
    use CanCustomizeProcess;
    use HasActionLogs;
    use HasPivotActionLogs;

    protected ?Closure $mutateRecordDataUsing = null;

    public static function getDefaultName(): ?string
    {
        return 'edit';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(function (BaseListResources|BaseViewResource|BaseManageResources|BaseRelationManager $livewire) {
            if ($livewire instanceof BaseViewResource || $livewire instanceof BaseManageResources) {
                return null;
            }

            return '';
        });

        $this->modalHeading(fn (): string => __('filament-actions::edit.single.modal.heading', ['label' => $this->getRecordTitle()]));
        $this->modalSubmitActionLabel(__('filament-actions::edit.single.modal.actions.save.label'));
        $this->successNotificationTitle(__('filament-actions::edit.single.notifications.saved.title'));

        $this->tableIcon(Heroicon::PencilSquare);
        $this->groupedIcon(Heroicon::PencilSquare);
        $this->iconSize(IconSize::Medium);

        $this->schema(fn (Schema $schema, BaseListResources|BaseViewResource|BaseManageResources|BaseRelationManager $livewire) => [
            ...$livewire->form($schema)->getComponents(),
            ...($livewire instanceof BaseRelationManager ? $livewire->getPivotComponents() : []),
        ]);

        $this->after(function (BaseListResources|BaseViewResource|BaseManageResources|BaseRelationManager $livewire, Model $record, EditAction $action) {
            if ($livewire instanceof BaseListResources || $livewire instanceof BaseViewResource) {
                ActionLog::modelUpdated($record);
            }

            if ($livewire instanceof BaseRelationManager) {
                if ($livewire->getRelationship() instanceof BelongsToMany) {
                    $this->pivotActionLog('Update Attached', $livewire, $record, $action);
                }
            }
        });

        $this->fillForm(function (HasActions&HasSchemas $livewire, Model $record, ?Table $table): array {
            $translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver();

            if ($translatableContentDriver) {
                $data = $translatableContentDriver->getRecordAttributesToArray($record);
            } else {
                $data = $record->attributesToArray();
            }

            $relationship = $table?->getRelationship();

            if ($relationship instanceof BelongsToMany) {
                $pivot = $record->getRelationValue($relationship->getPivotAccessor());

                $pivotColumns = $relationship->getPivotColumns();

                if ($translatableContentDriver) {
                    $data = [
                        ...$data,
                        ...Arr::only($translatableContentDriver->getRecordAttributesToArray($pivot), $pivotColumns),
                    ];
                } else {
                    $data = [
                        ...$data,
                        ...Arr::only($pivot->attributesToArray(), $pivotColumns),
                    ];
                }
            }

            if ($this->mutateRecordDataUsing) {
                $data = $this->evaluate($this->mutateRecordDataUsing, ['data' => $data]);
            }

            return $data;
        });

        $this->action(function (): void {
            $this->process(function (array $data, HasActions&HasSchemas $livewire, Model $record, ?Table $table): void {
                $relationship = $table?->getRelationship();

                $translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver();

                if ($relationship instanceof BelongsToMany) {
                    $pivot = $record->getRelationValue($relationship->getPivotAccessor());

                    $pivotColumns = $relationship->getPivotColumns();
                    $pivotData = Arr::only($data, $pivotColumns);

                    if (count($pivotColumns)) {
                        if ($translatableContentDriver) {
                            $translatableContentDriver->updateRecord($pivot, $pivotData);
                        } else {
                            $pivot->update($pivotData);
                        }
                    }

                    $data = Arr::except($data, $pivotColumns);
                }

                if ($translatableContentDriver) {
                    $translatableContentDriver->updateRecord($record, $data);
                } else {
                    $record->update($data);
                }
            });

            $this->success();
        });
    }

    public function mutateRecordDataUsing(?Closure $callback): static
    {
        $this->mutateRecordDataUsing = $callback;

        return $this;
    }
}
