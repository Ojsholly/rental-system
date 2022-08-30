<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Equipment\CreateEquipmentRequest;
use App\Http\Requests\Equipment\UpdateEquipmentRequest;
use App\Http\Resources\Equipment\EquipmentResource;
use App\Http\Resources\Equipment\EquipmentResourceCollection;
use App\Services\Equipment\EquipmentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class EquipmentController extends Controller
{
    public EquipmentService $equipmentService;

    public function __construct(EquipmentService $equipmentService)
    {
        $this->equipmentService = $equipmentService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $equipments = $this->equipmentService->getEquipments([], [], request()->query());
        } catch (Throwable $exception) {
            report($exception);

            return response()->error('An error occurred while fetching equipments.', ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->success(new EquipmentResourceCollection($equipments), 'Equipments fetched successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateEquipmentRequest  $request
     * @return JsonResponse
     */
    public function store(CreateEquipmentRequest $request): JsonResponse
    {
        try {
            $equipment = $this->equipmentService->create($request->validated());
        } catch (Throwable $exception) {
            report($exception);

            return response()->error('An error occurred while creating equipment.', ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->success(new EquipmentResource($equipment), 'Equipment created successfully.', ResponseAlias::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            $equipment = $this->equipmentService->getEquipment($id);
        } catch (Throwable $exception) {
            if ($exception instanceof ModelNotFoundException) {
                return response()->error($exception->getMessage(), $exception->getCode());
            }

            report($exception);

            return response()->error('An error occurred while fetching equipment.', ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->success(new EquipmentResource($equipment), 'Equipment fetched successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateEquipmentRequest  $request
     * @param  string  $id
     * @return JsonResponse
     */
    public function update(UpdateEquipmentRequest $request, string $id): JsonResponse
    {
        try {
            $equipment = $this->equipmentService->updateEquipment($request->validated(), $id);
        } catch (Throwable $exception) {
            if ($exception instanceof ModelNotFoundException) {
                return response()->error($exception->getMessage(), $exception->getCode());
            }
            report($exception);

            return response()->error('An error occurred while updating equipment.', ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->success(new EquipmentResource($equipment), 'Equipment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->equipmentService->deleteEquipment($id);
        } catch (Throwable $exception) {
            if ($exception instanceof ModelNotFoundException) {
                return response()->error($exception->getMessage(), $exception->getCode());
            }
            report($exception);

            return response()->error('An error occurred while deleting equipment.', ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->success([], 'Equipment deleted successfully.');
    }
}
