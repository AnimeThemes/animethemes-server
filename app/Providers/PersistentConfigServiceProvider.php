<?php

namespace App\Providers;

use Illuminatech\Config\Providers\AbstractPersistentConfigServiceProvider;
use Illuminatech\Config\StorageContract;
use Illuminatech\Config\StorageDb;

class PersistentConfigServiceProvider extends AbstractPersistentConfigServiceProvider
{

    /**
     * Defines the storage for the persistent config.
     *
     * @return \Illuminatech\Config\StorageContract
     */
    protected function storage(): StorageContract
    {
        return (new StorageDb($this->app->make('db.connection')));
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
        ];
    }
}
