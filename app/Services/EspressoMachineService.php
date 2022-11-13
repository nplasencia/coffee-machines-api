<?php declare(strict_types = 1);

namespace App\Services;

use App\Exceptions\NoBeansException;
use App\Exceptions\NoWaterException;

class EspressoMachineService implements EspressoMachineInterface
{
    private const SINGLE_ESPRESSO_USED_WATER = 0.05;
    private const SINGLE_ESPRESSO_USED_BEANS = 1;

    public function __construct(
        private readonly WaterContainer $waterContainer,
        private readonly BeansContainer $beansContainer
    ) {}

    /**
     * @inheritDoc
     */
    public function makeEspresso(): float
    {
        return $this->makeCoffee(self::SINGLE_ESPRESSO_USED_WATER, self::SINGLE_ESPRESSO_USED_BEANS);
    }

    /**
     * @inheritDoc
     */
    public function makeDoubleEspresso(): float
    {
        return $this->makeCoffee(2 * self::SINGLE_ESPRESSO_USED_WATER, 2 * self::SINGLE_ESPRESSO_USED_BEANS);
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): string
    {
        $remainingWater = $this->waterContainer->getWater();
        $remainingBeans = $this->beansContainer->getBeans();

        return $this->getStatusMessage($remainingWater, $remainingBeans);
    }

    /**
     * @param float $neededWater
     * @param int $neededBeans
     * @return float
     * @throws NoBeansException
     * @throws NoWaterException
     */
    private function makeCoffee(float $neededWater, int $neededBeans): float
    {
        $this->validateContainers($neededWater, $neededBeans);

        $this->beansContainer->useBeans($neededBeans);
        return $this->waterContainer->useWater($neededWater);
    }

    /**
     * @param float $neededWater
     * @param int $neededBeans
     * @return void
     * @throws NoBeansException
     * @throws NoWaterException
     */
    private function validateContainers(float $neededWater, int $neededBeans): void
    {
        if ($this->beansContainer->getBeans() < $neededBeans) {
            throw new NoBeansException();
        }

        if ($this->waterContainer->getWater() < $neededWater) {
            throw new NoWaterException();
        }
    }

    private function getStatusMessage(float $remainingWater, int $remainingBeans): string
    {
        if ($remainingWater < self::SINGLE_ESPRESSO_USED_WATER && $remainingBeans < self::SINGLE_ESPRESSO_USED_BEANS) {
            return 'Add beans and water';
        }

        if ($remainingBeans < self::SINGLE_ESPRESSO_USED_BEANS) {
            return 'Add beans';
        }

        if ($remainingWater < self::SINGLE_ESPRESSO_USED_WATER) {
            return 'Add water';
        }

        return sprintf('%d Espressos left', $this->getRemainingEspressos($remainingWater, $remainingBeans));
    }

    private function getRemainingEspressos(float $remainingWater, int $remainingBeans): int
    {
        $espressosByWaterLeft = intval($remainingWater / self::SINGLE_ESPRESSO_USED_WATER);
        $espressosByBeansLeft = intval($remainingBeans / self::SINGLE_ESPRESSO_USED_BEANS);

        return min($espressosByWaterLeft, $espressosByBeansLeft);
    }
}
