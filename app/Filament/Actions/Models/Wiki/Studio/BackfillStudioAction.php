<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Studio;

use App\Actions\Models\Wiki\BackfillStudioAction as BackfillStudioActionAction;
use App\Enums\Models\Wiki\ImageFacet;
use App\Filament\Actions\Base\MarkAsReadAction;
use App\Filament\Actions\BaseAction;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use Exception;
use Filament\Forms\Components\Checkbox;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Sleep;

class BackfillStudioAction extends BaseAction implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    final public const string IMAGES = BackfillStudioActionAction::IMAGES;

    final public const string BACKFILL_LARGE_COVER = 'backfill_large_cover';

    public static function getDefaultName(): ?string
    {
        return 'backfill-studio';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.studio.backfill.name'));

        $this->visible(Gate::allows('create', Studio::class));

        $this->action(fn (Studio $record, array $data) => $this->handle($record, $data));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(Studio $studio, array $data): void
    {
        if ($studio->resources()->doesntExist()) {
            $this->failedLog(__('filament.actions.studio.backfill.message.resource_required_failure'));

            return;
        }

        $action = new BackfillStudioActionAction($studio, $this->getToBackfill($data));

        try {
            $result = $action->handle();
            if ($result->hasFailed()) {
                Notification::make()
                    ->body($result->getMessage())
                    ->warning()
                    ->actions([
                        MarkAsReadAction::make(),
                    ])
                    ->sendToDatabase(Auth::user());
            }
        } catch (Exception $e) {
            $this->failedLog($e);
        } finally {
            // Try not to upset third-party APIs
            Sleep::for(random_int(3, 5))->second();
        }
    }

    public function getSchema(Schema $schema): Schema
    {
        $studio = $this->getRecord();

        return $schema
            ->schema([
                Section::make(__('filament.actions.studio.backfill.fields.images.name'))
                    ->schema([
                        Checkbox::make(self::BACKFILL_LARGE_COVER)
                            ->label(__('filament.actions.studio.backfill.fields.images.large_cover.name'))
                            ->helperText(__('filament.actions.studio.backfill.fields.images.large_cover.help'))
                            ->default(fn (): bool => $studio instanceof Studio && $studio->images()->where(Image::ATTRIBUTE_FACET, ImageFacet::LARGE_COVER->value)->doesntExist()),
                    ]),
            ]);
    }

    /**
     * Get what should be backfilled.
     *
     * @param  array<string, mixed>  $components
     * @return array<string, ImageFacet[]>
     */
    protected function getToBackfill(array $components): array
    {
        $toBackfill = [];
        $toBackfill[self::IMAGES] = [];

        foreach ($this->getImagesMapping() as $component => $facets) {
            if (Arr::get($components, $component) === true) {
                $toBackfill[self::IMAGES] = array_merge($toBackfill[self::IMAGES], $facets);
            }
        }

        return $toBackfill;
    }

    /**
     * Get the images for mapping.
     *
     * @return array<string, array<int, ImageFacet>>
     */
    protected function getImagesMapping(): array
    {
        return [
            self::BACKFILL_LARGE_COVER => [ImageFacet::LARGE_COVER],
        ];
    }
}
