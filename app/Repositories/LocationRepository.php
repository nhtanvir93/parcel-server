<?php
namespace App\Repositories;

use App\Models\Location;
use App\Repositories\Interfaces\LocationRepositoryInterface;

class LocationRepository extends BaseRepository implements LocationRepositoryInterface
{
    public function __construct(Location $model)
    {
        parent::__construct($model);
    }

    public function getAllMatches($location) {
        return $this->model
            ->select('id', 'name', 'longitude', 'latitude')
            ->where('name', 'like', $location . '%')
            ->get();
    }
}