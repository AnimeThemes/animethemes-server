<?php

declare(strict_types=1);

namespace App\Providers;

use App\Constants\Config\FlagConstants;
use App\Constants\Config\WikiConstants;
use App\Models\Wiki\Video;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Validation\Rule;
use Illuminatech\Config\Providers\AbstractPersistentConfigServiceProvider;
use Illuminatech\Config\StorageContract;
use Illuminatech\Config\StorageDb;

/**
 * Class PersistentConfigServiceProvider.
 */
class PersistentConfigServiceProvider extends AbstractPersistentConfigServiceProvider
{
    /**
     * Defines the storage for the persistent config.
     *
     * @return StorageContract
     *
     * @throws BindingResolutionException
     */
    protected function storage(): StorageContract
    {
        return new StorageDb($this->app->make('db.connection'));
    }

    /**
     * Defines configuration items, which values should be placed in persistent storage.
     *
     * @return array
     */
    protected function items(): array
    {
        return [
            FlagConstants::ALLOW_VIDEO_STREAMS_FLAG_QUALIFIED => [
                'label' => __('Allow Video Streams'),
                'rules' => ['sometimes', 'required', 'boolean'],
                'cast' => 'boolean',
            ],
            FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED => [
                'label' => __('Allow Discord Notifications'),
                'rules' => ['sometimes', 'required', 'boolean'],
                'cast' => 'boolean',
            ],
            FlagConstants::ALLOW_VIEW_RECORDING_FLAG_QUALIFIED => [
                'label' => __('Allow View Recording'),
                'rules' => ['sometimes', 'required', 'boolean'],
                'cast' => 'boolean',
            ],
            WikiConstants::FEATURED_THEME_SETTING_QUALIFIED => [
                'label' => __('Featured Theme'),
                'rules' => ['nullable', 'string', Rule::exists(Video::TABLE, Video::ATTRIBUTE_BASENAME)],
                'cast' => 'string',
            ],
        ];
    }
}
