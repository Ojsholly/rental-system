<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Services\Rental\RentalService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class RentalStatsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param RentalService $rentalService
     * @return JsonResponse
     */
    public function __invoke(Request $request, RentalService $rentalService): JsonResponse
    {
        try {
            $start = $request->get('start') ?? Carbon::now()->subDays(30)->format('Y-m-d');
            $end = $request->get('end') ?? Carbon::now()->format('Y-m-d');

            $period = [Carbon::parse($start)->startOfDay(), Carbon::parse($end)->endOfDay()];

            $rentedBooksCount = $rentalService->getRentedBooksCount($period);
            $rentedEquipmentCount = $rentalService->getRentedEquipmentCount($period);
            $returnedBooksCount = $rentalService->getReturnedBooksCount($period);
            $returnedEquipmentCount = $rentalService->getReturnedEquipmentCount($period);
        } catch (Throwable $exception) {
            report($exception);

            return response()->error('An error occurred while fetching the stats.', ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->success(compact('rentedBooksCount', 'rentedEquipmentCount', 'returnedBooksCount', 'returnedEquipmentCount'),
            "Rental stats for {$start} - {$end} fetched successfully.");
    }
}
