<?php

namespace Nourayman\SearchJson;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    public function scopeSearchJson(Builder $query, string $field, $term, array $langs)
    {
        $service = new SearchJsonService();
        return $service->searchJson($query, $field, $term, $langs);
    }
}
