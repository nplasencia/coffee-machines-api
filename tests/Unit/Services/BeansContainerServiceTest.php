<?php declare(strict_types = 1);

namespace Tests\Unit\Services;

use App\Gateways\ContainerGatewayInterface;
use App\Services\BeansContainerService;
use App\Utils\JsonUtils;
use PHPUnit\Framework\TestCase;
use App\Exceptions\ContainerFullException;

final class BeansContainerServiceTest extends TestCase
{
    private const CONTAINER_MAX_CAPACITY = 50;

    private BeansContainerService $beansContainerService;
    private ContainerGatewayInterface $gateway;

    protected function setUp(): void
    {
        $this->gateway = $this->createMock(ContainerGatewayInterface::class);
        $this->beansContainerService = new BeansContainerService($this->gateway, new JsonUtils());
    }

    public function testAddBeans_throwsExceptionWhenAddingMoreThanMaxCapacity(): void
    {
        $this->gateway->expects($this->once())->method('getData')
            ->willReturn($this->getBeansContainerJsonString(0));

        $this->expectException(ContainerFullException::class);

        $this->beansContainerService->addBeans(1000);
    }

    private function getBeansContainerJsonString(int $currentBeans): string
    {
        return sprintf('{"maxCapacity":%d,"currentBeansSpoons":%d}', self::CONTAINER_MAX_CAPACITY, $currentBeans);
    }

    /**
     * @dataProvider addBeansDataProvider
     */
    public function testAddBeans_success(int $currentBeans, int $beansToAdd, int $expectedBeansToSave): void
    {
        $this->gateway->expects($this->once())->method('getData')
            ->willReturn($this->getBeansContainerJsonString($currentBeans));

        $this->gateway->expects($this->once())->method('saveData')
            ->with($this->getBeansContainerJsonString($expectedBeansToSave));

        $this->beansContainerService->addBeans($beansToAdd);
    }

    public function addBeansDataProvider(): array
    {
        return [
            [0, 0, 0],
            [0, 1, 1],
            [1, 1, 2],
            [2, 4, 6],
            [49, 1, 50],
            [self::CONTAINER_MAX_CAPACITY, 0, self::CONTAINER_MAX_CAPACITY]
        ];
    }

    public function testUseBeans_returnsZeroWhenTryToUseMoreBeansThanExisting(): void
    {
        $this->gateway->expects($this->once())->method('getData')
            ->willReturn($this->getBeansContainerJsonString(0));

        $this->gateway->expects($this->never())->method('saveData');

        $this->assertSame(0, $this->beansContainerService->useBeans(1000));
    }

    /**
     * @dataProvider useBeansDataProvider
     */
    public function testUseBeans_success(
        int $currentBeans,
        int $beansToUse,
        int $beansToSave,
        int $expectedResult
    ): void {
        $this->gateway->expects($this->once())->method('getData')
            ->willReturn($this->getBeansContainerJsonString($currentBeans));

        $this->gateway->expects($this->once())->method('saveData')
            ->with($this->getBeansContainerJsonString($beansToSave));

        $this->assertSame($expectedResult, $this->beansContainerService->useBeans($beansToUse));
    }

    public function useBeansDataProvider(): array
    {
        return [
            [0, 0, 0, 0],
            [10, 1, 9, 1],
            [1, 1, 0, 1],
            [self::CONTAINER_MAX_CAPACITY, self::CONTAINER_MAX_CAPACITY, 0, self::CONTAINER_MAX_CAPACITY]
        ];
    }

    public function testGetBeans(): void
    {
        $currentBeans = 10;
        $this->gateway->expects($this->once())->method('getData')
            ->willReturn($this->getBeansContainerJsonString($currentBeans));

        $this->assertSame($currentBeans, $this->beansContainerService->getBeans());
    }
}
