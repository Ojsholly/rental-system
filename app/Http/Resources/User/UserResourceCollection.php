<?php

namespace App\Http\Resources\User;

use App\Traits\PaginationTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserResourceCollection extends ResourceCollection
{
    use PaginationTrait;

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'meta' => $this->metaData(),
            'users' => parent::toArray($request),
        ];
    }
}
