<?php
namespace App\GraphQL\Query;
use App\Models\Artist;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Folklore\GraphQL\Support\Facades\GraphQL;
use Folklore\GraphQL\Support\Query;

class ArtistQuery extends Query
{
    protected $attributes = [
        'name' => 'Artist Query',
        'description' => 'Query Artist'
    ];
    public function type()
    {
        return Type::listOf(GraphQL::type('Artist'));
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
            ]
        ];
    }
    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        $fields = $info->getFieldSelection();

        $artists = Artist::query();
        
        foreach ($fields as $field => $keys) {
            if ($field === 'themes') {
                $artists->with('themes');
            }
        }
        
        if (isset($args['id'])) {
            $artists->where('id',$args['id']);
        } else if (isset($args['name'])) {
            $artists->where('name',$args['name']);
        }
        return $artists->get();
    }
}