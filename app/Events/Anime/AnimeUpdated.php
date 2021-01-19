<?php

namespace App\Events\Anime;

use App\Models\Anime;

use NotificationChannels\Discord\DiscordMessage;

class AnimeUpdated extends AnimeEvent
{
    /**
     * The array of changes.
     *
     * @var array
     */
    protected $changes;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Anime $anime
     * @return void
     */
    public function __construct(Anime $anime)
    {
        parent::__construct($anime);
        //TODO: Clean this abomination up
        $this->changes = collect($anime->getOriginal())->only(collect($anime->getChanges())->forget('updated_at')->keys())->all();
    }

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $anime = $this->getAnime();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Anime Updated', [
            'description' => "Anime '{$anime->name}' has been updated.",
            'fields' => $this->getEmbedFields(),
        ]);
    }

    /**
     * Get Discord Embed Fields from changed attributes.
     * TODO: Bleh.
     *
     * @return array
     */
    private function getEmbedFields()
    {
        $anime = $this->getAnime();
        $embedFields = [];

        foreach ($this->changes as $name => $value) {
            $attributeField = [
                'name' => 'Attribute',
                'value' => $name,
                'inline' => true,
            ];
            $embedFields[] = $attributeField;

            $oldField = [
                'name' => 'Old',
                'value' => $value,
                'inline' => true,
            ];
            $embedFields[] = $oldField;

            $newField = [
                'name' => 'New',
                'value' => $anime->{$name},
                'inline' => true,
            ];
            $embedFields[] = $newField;
        }

        return $embedFields;
    }
}
