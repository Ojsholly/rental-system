<?php

namespace App\Http\Resources\Equipment;

use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentResource extends JsonResource
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
            'name' => $this->name,
            'manufacturer' => $this->manufacturer,
            'description' => $this->description,
            'serial_number' => $this->serial_number,
            'model_number' => $this->model_number,
            'created_at' => $this->created_at->toDayDateTimeString(),
            'updated_at' => $this->updated_at->diffForHumans(),
            'deleted_at' => $this->deleted_at?->toDayDateTimeString(),
        ];
    }
}
