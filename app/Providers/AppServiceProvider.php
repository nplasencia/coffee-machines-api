<?php

namespace App\Providers;

use App\Gateways\BeanContainerFilesystemGateway;
use App\Gateways\ContainerGatewayInterface;
use App\Gateways\WaterContainerFilesystemGateway;
use App\Services\BeansContainer;
use App\Services\BeansContainerService;
use App\Services\EspressoMachineInterface;
use App\Services\EspressoMachineService;
use App\Services\WaterContainer;
use App\Services\WaterContainerService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(EspressoMachineInterface::class, EspressoMachineService::class);

        $this->app->bind(WaterContainer::class, WaterContainerService::class);

        $this->app->bind(BeansContainer::class, BeansContainerService::class);

        $this->app->when(WaterContainerService::class)
            ->needs(ContainerGatewayInterface::class)
            ->give(WaterContainerFilesystemGateway::class);

        $this->app->when(BeansContainerService::class)
            ->needs(ContainerGatewayInterface::class)
            ->give(BeanContainerFilesystemGateway::class);
    }
}
