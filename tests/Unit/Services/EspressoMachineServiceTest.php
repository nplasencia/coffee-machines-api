<?php declare(strict_types = 1);

namespace Tests\Unit\Services;

use App\Exceptions\NoBeansException;
use App\Exceptions\NoWaterException;
use App\Services\BeansContainer;
use App\Services\EspressoMachineService;
use App\Services\WaterContainer;
use PHPUnit\Framework\TestCase;

final class EspressoMachineServiceTest extends TestCase
{
    private const SINGLE_ESPRESSO_USED_WATER = 0.05;
    private const SINGLE_ESPRESSO_USED_BEANS = 1;

    private EspressoMachineService $espressoMachine;

    private WaterContainer $waterContainer;
    private BeansContainer $beansContainer;

    protected function setUp(): void
    {
        $this->waterContainer = $this->createMock(WaterContainer::class);
        $this->beansContainer = $this->createMock(BeansContainer::class);

        $this->espressoMachine = new EspressoMachineService($this->waterContainer, $this->beansContainer);
    }

    public function testMakeEspresso_validation_throwsNoBeansException(): void
    {
        $this->beansContainer->expects($this->once())->method('getBeans')->willReturn(0);

        $this->expectException(NoBeansException::class);

        $this->espressoMachine->makeEspresso();
    }

    public function testMakeEspresso_validation_throwsNoWaterException(): void
    {
        $this->beansContainer->expects($this->once())->method('getBeans')->willReturn(99);
        $this->waterContainer->expects($this->once())->method('getWater')->willReturn(0.04);

        $this->expectException(NoWaterException::class);

        $this->espressoMachine->makeEspresso();
    }

    public function testMakeEspresso_success(): void
    {
        $this->beansContainer->expects($this->once())->method('getBeans')->willReturn(1);
        $this->waterContainer->expects($this->once())->method('getWater')->willReturn(0.05);

        $this->beansContainer->expects($this->once())->method('useBeans')
            ->with(self::SINGLE_ESPRESSO_USED_BEANS);
        $this->waterContainer->expects($this->once())->method('useWater')
            ->with(self::SINGLE_ESPRESSO_USED_WATER)
            ->willReturn(self::SINGLE_ESPRESSO_USED_WATER);

        $this->assertSame(self::SINGLE_ESPRESSO_USED_WATER, $this->espressoMachine->makeEspresso());
    }

    public function testMakeDoubleEspresso_validation_throwsNoBeansException(): void
    {
        $this->beansContainer->expects($this->once())->method('getBeans')->willReturn(0);

        $this->expectException(NoBeansException::class);

        $this->espressoMachine->makeDoubleEspresso();
    }

    public function testMakeDoubleEspresso_validation_throwsNoWaterException(): void
    {
        $this->beansContainer->expects($this->once())->method('getBeans')->willReturn(99);
        $this->waterContainer->expects($this->once())->method('getWater')->willReturn(0.09);

        $this->expectException(NoWaterException::class);

        $this->espressoMachine->makeDoubleEspresso();
    }

    public function testMakeDoubleEspresso_success(): void
    {
        $this->beansContainer->expects($this->once())->method('getBeans')->willReturn(2);
        $this->waterContainer->expects($this->once())->method('getWater')->willReturn(0.1);

        $this->beansContainer->expects($this->once())->method('useBeans')
            ->with(2 * self::SINGLE_ESPRESSO_USED_BEANS);
        $this->waterContainer->expects($this->once())->method('useWater')
            ->with(2 * self::SINGLE_ESPRESSO_USED_WATER)
            ->willReturn(2 * self::SINGLE_ESPRESSO_USED_WATER);

        $this->assertSame(2 * self::SINGLE_ESPRESSO_USED_WATER, $this->espressoMachine->makeDoubleEspresso());
    }

    /**
     * @dataProvider getStatusDataProvider
     * @param float $water
     * @param int $beans
     * @param string $expectedMessage
     * @return void
     */
    public function testGetStatus(float $water, int $beans, string $expectedMessage): void
    {
        $this->waterContainer->expects($this->once())->method('getWater')->willReturn($water);
        $this->beansContainer->expects($this->once())->method('getBeans')->willReturn($beans);

        $actual = $this->espressoMachine->getStatus();
        $this->assertSame($expectedMessage, $actual);
    }

    public function getStatusDataProvider(): array
    {
        return [
            [0.0, 0, 'Add beans and water'],
            [0.00, 99, 'Add water'],
            [0.04, 99, 'Add water'],
            [99.99, 0, 'Add beans'],
            [0.05, 1, '1 Espressos left'],
            [0.05, 2, '1 Espressos left'],
            [0.09, 3, '1 Espressos left'],
            [0.10, 4, '2 Espressos left'],
        ];
    }
}
