<?php declare(strict_types = 1);

namespace Tests\Unit\Services;

use App\Gateways\ContainerGatewayInterface;
use App\Services\WaterContainerService;
use App\Utils\JsonUtils;
use PHPUnit\Framework\TestCase;
use App\Exceptions\ContainerFullException;

final class WaterContainerServiceTest extends TestCase
{
    private const CONTAINER_MAX_CAPACITY = 2;

    private WaterContainerService $waterContainerService;
    private ContainerGatewayInterface $gateway;

    protected function setUp(): void
    {
        $this->gateway = $this->createMock(ContainerGatewayInterface::class);
        $this->waterContainerService = new WaterContainerService($this->gateway, new JsonUtils());
    }

    public function testAddWater_throwsExceptionWhenAddingMoreThanMaxCapacity(): void
    {
        $this->gateway->expects($this->once())->method('getData')
            ->willReturn($this->getWaterContainerJsonString(0));

        $this->expectException(ContainerFullException::class);

        $this->waterContainerService->addWater(1000);
    }

    private function getWaterContainerJsonString(float $currentWater): string
    {
        return sprintf('{"maxCapacity":%.2f,"currentWater":%.2f}', self::CONTAINER_MAX_CAPACITY, $currentWater);
    }

    /**
     * @dataProvider addWaterDataProvider
     */
    public function testAddWater_success(float $currentWater, float $waterToAdd, string $expectedJsonString): void
    {
        $this->gateway->expects($this->once())->method('getData')
            ->willReturn($this->getWaterContainerJsonString($currentWater));

        $this->gateway->expects($this->once())->method('saveData')->with($expectedJsonString);

        $this->waterContainerService->addWater($waterToAdd);
    }

    public function addWaterDataProvider(): array
    {
        return [
            [.0, .0, '{"maxCapacity":2,"currentWater":0}'],
            [.0, .5, '{"maxCapacity":2,"currentWater":0.5}'],
            [.5, 1.5, '{"maxCapacity":2,"currentWater":2}'],
            [self::CONTAINER_MAX_CAPACITY, 0.0, '{"maxCapacity":2,"currentWater":2}']
        ];
    }

    public function testUseWater_returnsZeroWhenTryToUseMoreWaterThanExisting(): void
    {
        $this->gateway->expects($this->once())->method('getData')
            ->willReturn($this->getWaterContainerJsonString(0));

        $this->gateway->expects($this->never())->method('saveData');

        $this->assertSame(.0, $this->waterContainerService->useWater(1000));
    }

    /**
     * @dataProvider useWaterDataProvider
     */
    public function testUseWater_success(
        float $currentWater,
        float $waterToUse,
        string $expectedJsonString,
        float $expectedResult
    ): void {
        $this->gateway->expects($this->once())->method('getData')
            ->willReturn($this->getWaterContainerJsonString($currentWater));

        $this->gateway->expects($this->once())->method('saveData')->with($expectedJsonString);

        $this->assertSame($expectedResult, $this->waterContainerService->useWater($waterToUse));
    }

    public function useWaterDataProvider(): array
    {
        return [
            [.0, .0, '{"maxCapacity":2,"currentWater":0}', .0],
            [.9, .1, '{"maxCapacity":2,"currentWater":0.8}', .1],
            [.1, .1, '{"maxCapacity":2,"currentWater":0}', .1],
            [self::CONTAINER_MAX_CAPACITY, self::CONTAINER_MAX_CAPACITY, '{"maxCapacity":2,"currentWater":0}', self::CONTAINER_MAX_CAPACITY]
        ];
    }

    public function testGetWater(): void
    {
        $currentWater = 1.7;
        $this->gateway->expects($this->once())->method('getData')
            ->willReturn($this->getWaterContainerJsonString($currentWater));

        $this->assertSame($currentWater, $this->waterContainerService->getWater());
    }
}
