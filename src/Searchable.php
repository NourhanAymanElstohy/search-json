<?php

namespace Nourayman\SearchJson;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    /**
     * Search for a specific key within a JSON string and return its value.
     *
     * @param string $field The field containing the JSON string.
     * @param string $key The key to search for within the JSON string.
     * @return Builder
     */
    public function scopeSearchJson(Builder $query, string $field, $term, array $langs)
    {
        $service = new SearchJsonService();
        return $service->searchJson($query, $field, $term, $langs);
    }
}
