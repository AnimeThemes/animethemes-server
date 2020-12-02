<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\BaseCollection;

class RandomController extends BaseController {

    public function show($resource) {
        try {
            // Use Container to resolve class
            $resourceClass = resolve('App\Models\\'.$resource);

            $resourceBuilder = $resourceClass::with($this->parser->getIncludePaths($resourceClass::$allowedIncludePaths));

            // apply filters and limit
            $resourceBuilder = $resourceClass::applyFilters($resourceBuilder, $this->parser);
            $resources = $resourceBuilder->inRandomOrder()->limit($this->parser->getAmount())->get();

            // resolve correct resource collection class
            $resourceCollection = BaseCollection::getCollection($resource, $resources, $this->parser);
            return $resourceCollection->toResponse(request());
        } catch (\Throwable $ex) {
            return Abort(404);
        }
    }
}
