<?php
namespace App\GraphQL\Query;
use App\Models\Serie;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Folklore\GraphQL\Support\Facades\GraphQL;
use Folklore\GraphQL\Support\Query;

class SerieQuery extends Query
{
    protected $attributes = [
        'name' => 'Serie Query',
        'description' => 'Query Serie'
    ];
    public function type()
    {
        return Type::listOf(GraphQL::type('Serie'));
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

        $series = Serie::query();
        
        foreach ($fields as $field => $keys) {
            if ($field === 'animes') {
                $series->with('animes');
            }
        }
        
        if (isset($args['id'])) {
            $series->where('id',$args['id']);
        } else if (isset($args['name'])) {
            $series->where('name',$args['name']);
        }
        return $series->get();
    }
}