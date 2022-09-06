<?php

namespace App\Models\Searchable;

use AjCastro\Searchable\BaseSearch;
use Illuminate\Support\Facades\DB;

class SearchWithRelations extends BaseSearch
{
    private array $relations;

    /**
     * @param array $relations
     *
     * @return \App\Models\Searchable\SearchWithRelations
     */
    public function setRelations(array $relations): SearchWithRelations
    {
        $this->relations = $relations;

        return $this;
    }
    /**
     * Apply a search query.
     *
     * @param  string $searchStr
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function search($searchStr)
    {
        $this->searchStr  = $searchStr;
        $columnsToCompare = $this->columnsToCompare();
        $query            = $this->query;

        if (count($columnsToCompare) === 0) {
            return $query;
        }

        $query->where(function ($query) use ($columnsToCompare, $searchStr) {
            $parsedStr = $this->parseSearchStr($searchStr);

            foreach ($columnsToCompare as $column) {
                $query->orWhere(DB::raw($column), 'LIKE', $parsedStr);
            }

            foreach ($this->relations as $relation => $columns) {
                $query->orWhereHas($relation, function ($query) use ($parsedStr, $columns) {
                    foreach ($columns as $column) {
                        $query->where($column, 'LIKE', $parsedStr);
                    }
                });
            }
        });

        return $query;
    }
}
