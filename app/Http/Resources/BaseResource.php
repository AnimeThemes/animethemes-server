<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

abstract class BaseResource extends JsonResource
{
    use ResolvesFields;

    /**
     * Customize the response for a request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\JsonResponse  $response
     * @return void
     */
    public function withResponse($request, $response)
    {
        // Fields Parameter: The comma-separated list of fields to include by dot notation
        $fields_query = strval(request('fields'));
        if (!empty($fields_query)) {
            $original_data = $response->getData(true);
            $original_dot_keys = array_keys(Arr::dot($original_data));

            $fields_data = [];

            $fields = explode(',', $fields_query);
            foreach ($fields as $field) {
                $resolved_fields = $this->resolveFields($original_dot_keys, $field);
                foreach ($resolved_fields as $resolved_field) {
                    if (Arr::has($original_data, $resolved_field)) {
                        Arr::set($fields_data, $resolved_field, Arr::get($original_data, $resolved_field));
                    }
                }
            }

            // If we have at least one valid field selection, update the response data
            if (!empty($fields_data)) {
                $response->setData($fields_data);
            }
        }
    }
}
