<?php

namespace App\Traits;

trait PaginationTrait
{
    /**
     * @return array
     */
    public function metaData(): array
    {
        return [
            'total' => $this->total(),
            'per_page' => $this->perPage(),
            'current_page' => $this->currentPage(),
            'last_page' => $this->lastPage(),
            'first_page_url' => $this->getOptions()['path'].'?'.$this->getOptions()['pageName'].'=1',
            'last_page_url' => $this->getOptions()['path'].'?'.$this->getOptions()['pageName'].'='.$this->lastPage(),
            'next_page_url' => $this->nextPageUrl(),
            'prev_page_url' => $this->previousPageUrl(),
            'path' => $this->path(),
            'from' => $this->firstItem(),
            'to' => $this->lastItem(),
        ];
    }
}
