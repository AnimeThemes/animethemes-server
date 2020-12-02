<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class BaseCollection extends ResourceCollection
{
    /**
     * Sparse field set specified by the client.
     *
     * @var \App\JsonApi\QueryParser
     */
    protected $parser;

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @param \App\JsonApi\QueryParser $parser
     * @return void
     */
    public function __construct($resource, $parser)
    {
        parent::__construct($resource);

        $this->parser = $parser;
    }

    public static function getCollection($resource, ...$parameters)
    {
        switch ($resource) {
            case 'anime':
                $collection = AnimeCollection::make(...$parameters);
                break;
            case 'artist':
                $collection = ArtistCollection::make(...$parameters);
                break;
            case 'announcement':
                $collection = AnnouncementCollection::make(...$parameters);
                break;
            case 'entry':
                $collection = EntryCollection::make(...$parameters);
                break;
            case 'series':
                $collection = SeriesCollection::make(...$parameters);
                break;
            case 'song':
                $collection = SongCollection::make(...$parameters);
                break;
            case 'synonym':
                $collection = SynonymCollection::make(...$parameters);
                break;
            case 'resource':
                $collection = ExternalResourceCollection::make(...$parameters);
                break;
            case 'video':
                $collection = VideoCollection::make(...$parameters);
                break;
            case 'image':
                $collection = ImageCollection::make(...$parameters);
                break;
            
            default:
                $collection = static::make(...$parameters);
                break;
        }

        return $collection;
    }
}
