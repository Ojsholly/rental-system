<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => Str::ucfirst($this->name),
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'created_at' => $this->created_at->toDayDateTimeString(),
            'updated_at' => $this->updated_at->diffForHumans(),
            'deleted_at' => $this->deleted_at?->toDayDateTimeString(),
        ];
    }
}
