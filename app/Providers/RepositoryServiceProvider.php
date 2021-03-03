<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\LocationRepositoryInterface;
use App\Repositories\LocationRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public $singletons = [
        LocationRepositoryInterface::class => LocationRepository::class
    ];
}
