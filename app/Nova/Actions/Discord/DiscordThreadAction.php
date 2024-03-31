<?php

declare(strict_types=1);

namespace App\Nova\Actions\Discord;

use App\Constants\Config\ServiceConstants;
use App\Enums\Discord\EmbedColor;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ImageFacet;
use App\Models\Wiki\Image;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Laravel\Nova\Fields\Text;

class DiscordThreadAction extends Action
{
    /**
     * Get the displayable name of the action.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.actions.anime.discord.thread.name');
    }

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $anime = $models->first();
        $name = $fields->get('name');

        $imagePath = $anime->images()->where(Image::ATTRIBUTE_FACET, ImageFacet::COVER_LARGE)->first()->path;
        /** @var \Illuminate\Filesystem\FilesystemAdapter */
        $imageDisk = Storage::disk(Config::get('image.disk'));

        $animepage = json_decode(file_get_contents(base_path('composer.json')), true)['homepage'].'anime/';
        $description = '**Synopsis:** '.strip_tags($anime->synopsis)."\n\n".'**Link:** '.$animepage.$anime->slug;

        Http::withToken(Config::get('services.discord.token'), 'Bot')
            ->asMultipart()
            ->attach('file', file_get_contents($imageDisk->url($imagePath)), 'image.jpg')
            ->post("https://discord.com/api/v10/channels/{$this->getDiscordChannel()}/threads", [
                'payload_json' => json_encode([
                    'name' => $name,
                    'applied_tags' => $this->getAppliedTags($anime->season->value),
                    'message' => [
                        'embeds' => [
                            [
                                'color' => EmbedColor::PURPLE->value,
                                'title' => $anime->name,
                                'description' => $description,
                            ]
                        ],
                    ]
                ])
            ])->throw();

        return $models;
    }

    /**
     * Get Discord forum channel the thread will be created to.
     *
     * @return string
     */
    protected function getDiscordChannel(): string
    {
        return Config::get(ServiceConstants::SUBMISSIONS_DISCORD_CHANNEL_QUALIFIED);
    }

    /**
     * Get the IDs of the tags applied to the thread.
     * 
     * @param  int  $season
     * @return array
     */
    protected function getAppliedTags(int $season): array
    {
        return match ($season) {
            AnimeSeason::WINTER->value => [Config::get('services.discord.submissions_forum_tags.winter')],
            AnimeSeason::SPRING->value => [Config::get('services.discord.submissions_forum_tags.spring')],
            AnimeSeason::SUMMER->value => [Config::get('services.discord.submissions_forum_tags.summer')],
            AnimeSeason::FALL->value => [Config::get('services.discord.submissions_forum_tags.fall')],
            default => [],
        };
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        $anime = $request->findModelQuery()->first();

        return array_merge(
            parent::fields($request),
            [
                Text::make(__('nova.actions.discord.thread.name'), 'name')
                    ->default($anime->name ?? '')
                    ->required()
                    ->rules(['required', 'max:100'])
                    ->maxlength(100)
                    ->enforceMaxlength()
                    ->help(__('nova.actions.discord.thread.help'))
            ]
        );
    }
}