<?php declare(strict_types = 1);

namespace App\Services;

use App\Gateways\ContainerGatewayInterface;
use App\Utils\JsonUtils;
use App\Exceptions\ContainerFullException;
use stdClass;

class WaterContainerService implements WaterContainer
{
    public function __construct(
        private readonly ContainerGatewayInterface $waterContainerGateway,
        private readonly JsonUtils $jsonUtils
    ) {}

    public function addWater(float $litres): void
    {
        $waterContainer = $this->getWaterContainerObject();

        $waterContainer->currentWater = round($waterContainer->currentWater + $litres, 2);
        if ($waterContainer->currentWater > $waterContainer->maxCapacity) {
            throw new ContainerFullException();
        }

        $this->updateWaterContainerInfo($waterContainer);
    }

    public function useWater(float $litres): float
    {
        $waterContainer = $this->getWaterContainerObject();

        $waterContainer->currentWater = round($waterContainer->currentWater - $litres, 2);
        if ($waterContainer->currentWater < 0) {
            return 0;
        }

        $this->updateWaterContainerInfo($waterContainer);
        return $litres;
    }

    public function getWater(): float
    {
        $waterContainer = $this->getWaterContainerObject();
        return $waterContainer->currentWater;
    }

    private function getWaterContainerObject(): stdClass
    {
        $data = $this->waterContainerGateway->getData();
        return $this->jsonUtils->jsonDecodeThrowsOnError($data);
    }

    private function updateWaterContainerInfo(stdClass $waterContainer): bool
    {
        $data = $this->jsonUtils->jsonEncodeThrowsOnError($waterContainer);
        return $this->waterContainerGateway->saveData($data);
    }
}
