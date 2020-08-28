<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

abstract class BaseResource extends JsonResource
{
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

    /**
     * Resolve response data key for selected field
     *
     * @param array $original_dot_keys the unfiltered response data dot notation keys
     * @param string $field the selected field
     * @return array the list of response data dot notation keys that match the selected field pattern
     */
    protected function resolveFields($original_dot_keys, $field) : array {
        // Nothing to do if a canonical path is given, just exit early
        if (strpos($field, '*') === false) {
            return [$field];
        }

        // Return all dot notation keys that match the wildcard pattern
        $wildcard_field_pattern = $this->getWildcardPattern($field);
        return preg_grep($wildcard_field_pattern, $original_dot_keys);
    }

    /**
     * Get matching pattern for selected field based on dot notation wildcard
     *
     * @param string $field the selected field containing at least one wildcard
     * @return string the matching pattern for the selected wildcard field
     */
    protected function getWildcardPattern($field) : string {
        // Leaf node pattern matching
        // Example: "*.link" should match any "link" leaf node
        if (strpos($field, '*') === 0) {
            return '/^' . preg_replace(['/(\.)/', '/(\*)/'], ['\.', '.*'], $field) . '$/';
        }

        // Canonical pattern matching
        // Example: "anime.*.name" should match anime.0.name, anime.1.name, etc
        // Example: "anime.*.themes" should match anime.0.themes and all of its children
        return '/^' . preg_replace('/(\*)/', 'd+', preg_quote($field, '/')) . '/';
    }
}
