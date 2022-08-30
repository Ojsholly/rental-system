<?php

namespace App\Services\Equipment;

use App\Models\Equipment;
use App\Services\Service;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class EquipmentService extends Service
{
    /**
     * @param  array  $data
     * @return mixed
     */
    public function create(array $data): mixed
    {
        return Equipment::create($data);
    }

    /**
     * @param  string  $uuid
     * @return mixed
     */
    public function getEquipment(string $uuid)
    {
        return Cache::remember('equipment'.$uuid, now()->addDays(3), function () use ($uuid) {
            $equipment = Equipment::findByUuid($uuid);

            throw_if(! $equipment, new ModelNotFoundException('Requested equipment not found.', ResponseAlias::HTTP_NOT_FOUND));

            return $equipment;
        });
    }

    /**
     * @param  array  $params
     * @param  array  $relations
     * @param  array  $pagination
     * @return mixed
     */
    public function getEquipments(array $params = [], array $relations = [], array $pagination = []): mixed
    {
        return Equipment::getEquipments()->with($relations)->where($params)
            ->paginate(data_get($pagination, 'perPage', 100));
    }

    /**
     * @param  array  $data
     * @param  string  $uuid
     * @return mixed
     */
    public function updateEquipment(array $data, string $uuid): mixed
    {
        $equipment = $this->getEquipment($uuid);

        $equipment->update($data);

        $equipment->refresh();

        Cache::put('equipment'.$uuid, $equipment, now()->addDays(3));

        return $equipment;
    }

    /**
     * @param  string  $uuid
     * @return mixed
     */
    public function deleteEquipment(string $uuid): mixed
    {
        $equipment = $this->getEquipment($uuid);

        $equipment->delete();

        Cache::forget('equipment'.$uuid);

        return $equipment->trashed();
    }

    /**
     * @param  string  $searchTerm
     * @param  array  $pagination
     * @return LengthAwarePaginator
     */
    public function search(string $searchTerm, array $pagination = []): LengthAwarePaginator
    {
        return Equipment::search($searchTerm)->paginate(data_get($pagination, 'perPage', 100));
    }
}
