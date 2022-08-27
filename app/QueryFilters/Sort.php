<?php

namespace App\QueryFilters;

class Sort extends Filter
{
    /**
     * Sort the records via the sort parameter in the request.
     *
     * @param $builder
     * @return mixed
     */
    protected function applyFilter($builder): mixed
    {
        return $builder->orderBy('created_at', request($this->filterName()));
    }
}
