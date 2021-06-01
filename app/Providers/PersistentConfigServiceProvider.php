<?php declare(strict_types=1);

namespace App\Providers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminatech\Config\Providers\AbstractPersistentConfigServiceProvider;
use Illuminatech\Config\StorageContract;
use Illuminatech\Config\StorageDb;

/**
 * Class PersistentConfigServiceProvider
 * @package App\Providers
 */
class PersistentConfigServiceProvider extends AbstractPersistentConfigServiceProvider
{
    /**
     * Defines the storage for the persistent config.
     *
     * @return StorageContract
     * @throws BindingResolutionException
     */
    protected function storage(): StorageContract
    {
        return new StorageDb($this->app->make('db.connection'));
    }

    /**
     * Defines configuration items, which values should be placed in persistent storage.
     *
     * @return array persistent config items.
     */
    protected function items(): array
    {
        return [
            'app.allow_video_streams' => [
                'label' => __('Allow Video Streams'),
                'rules' => ['sometimes', 'required', 'boolean'],
                'cast' => 'boolean',
            ],
            'app.allow_discord_notifications' => [
                'label' => __('Allow Discord Notifications'),
                'rules' => ['sometimes', 'required', 'boolean'],
                'cast' => 'boolean',
            ],
        ];
    }
}
