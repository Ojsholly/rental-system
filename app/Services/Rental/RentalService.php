<?php

namespace App\Services\Rental;

use App\Models\Rental;
use App\Services\Service;

class RentalService extends Service
{
    /**
     * @param array $period
     * @return int
     */
    public function getRentedBooksCount(array $period = []): int
    {
        return Rental::when(! empty($period), function ($query) use ($period) {
            $query->whereBetween('created_at', $period);
        })->bookRentals()->count();
    }

    /**
     * @param array $period
     * @return int
     */
    public function getRentedEquipmentCount(array $period = []): int
    {
        return Rental::when(! empty($period), function ($query) use ($period){
            $query->whereBetween('created_at', $period);
        })->equipmentRentals()->count();
    }

    /**
     * @param array $period
     * @return int
     */
    public function getReturnedBooksCount(array $period = []): int
    {
        return Rental::when(! empty($period), function ($query) use ($period){
            $query->whereBetween('book_returned_at', $period);
        })->returnedBooks()->count();
    }

    /**
     * @param array $period
     * @return int
     */
    public function getReturnedEquipmentCount(array $period = []): int
    {
        return Rental::when(!empty($period), function ($query) use ($period) {
            $query->whereBetween('equipment_returned_at', $period);
        })->returnedEquipment()->count();
    }
}
