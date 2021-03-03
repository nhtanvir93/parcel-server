<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LocationRequest;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\Interfaces\LocationRepositoryInterface;

class LocationController extends Controller
{
    private $locationRepository;

    public function __construct(LocationRepositoryInterface $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    public function store(LocationRequest $request) {
        try {
            $this->locationRepository->create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Location is created successfully'
            ],Response::HTTP_OK);

        } catch (QueryException $exception) {

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getAllMatches ($location) {
        $locations = $this->locationRepository->getAllMatches($location, request()->query('except_longitude'), request()->query('except_latitude'));

        return response()->json([
            'success' => $locations->count() == 0 ? false : true,
            'data' => $locations
        ],Response::HTTP_OK);
    }
}
