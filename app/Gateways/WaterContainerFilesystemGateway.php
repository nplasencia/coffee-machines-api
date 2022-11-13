<?php declare(strict_types = 1);

namespace App\Gateways;

class WaterContainerFilesystemGateway extends AbstractContainerFilesystemGateway
{
    private const WATER_INFO_FILENAME = 'water_container.json';

    public function __construct()
    {
        parent::__construct(self::WATER_INFO_FILENAME);
    }
}
