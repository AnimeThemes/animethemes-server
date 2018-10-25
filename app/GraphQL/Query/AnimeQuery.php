<?php
namespace App\GraphQL\Query;
use App\Models\Anime;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Folklore\GraphQL\Support\Facades\GraphQL;
use Folklore\GraphQL\Support\Query;

class AnimeQuery extends Query
{
    protected $attributes = [
        'name' => 'Animes Query',
        'description' => 'Query Anime'
    ];
    public function type()
    {
        return Type::listOf(GraphQL::type('Anime'));
    }
    
    public function args()
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::int()
            ],
            'name' => [
                'name' => 'name',
                'type' => Type::string()
            ],
            'collection' => [
                'name' => 'collection',
                'type' => Type::int()
            ],
            'season' => [
                'name' => 'season',
                'type' => Type::int()
            ],
            'mal_id' => [
                'name' => 'mal_id',
                'type' => Type::int()
            ],
            'anilist_id' => [
                'name' => 'anilist_id',
                'type' => Type::int()
            ],
            'kitsu_id' => [
                'name' => 'kitsu_id',
                'type' => Type::int()
            ],
            'anidb_id' => [
                'name' => 'anidb_id',
                'type' => Type::int()
            ],
        ];
    }
    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        $fields = $info->getFieldSelection();

        $animes = Anime::query();
        
        foreach ($fields as $field => $keys) {
            if ($field === 'names') {
                $animes->with('names');
            }
    
            if ($field === 'themes') {
                $animes->with('themes');
            }
        }
        
        if (isset($args['id'])) {
            $animes->where('id',$args['id']);
        } else {
            if (isset($args['name'])) {
                $animes->where('name',$args['name']);
            }
            if (isset($args['collection'])) {
                $animes->where('collection',$args['collection']);
            }
            if (isset($args['season'])) {
                $animes->where('season',$args['season']);
            }

            if (isset($args['mal_id'])) {
                $animes->where('mal_id',$args['mal_id']);
            } else if (isset($args['anilist_id'])) {
                $animes->where('anilist_id',$args['anilist_id']);
            } else if (isset($args['kitsu_id'])) {
                $animes->where('kitsu_id',$args['kitsu_id']);
            } else if (isset($args['anidb_id'])) {
                $animes->where('anidb_id',$args['anidb_id']);
            }
        }
        return $animes->get();
    }
}