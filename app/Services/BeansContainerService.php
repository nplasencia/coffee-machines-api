<?php declare(strict_types = 1);

namespace App\Services;

use App\Gateways\ContainerGatewayInterface;
use App\Utils\JsonUtils;
use App\Exceptions\ContainerFullException;
use stdClass;

class BeansContainerService implements BeansContainer
{
    public function __construct(
        private readonly ContainerGatewayInterface $beanContainerGateway,
        private readonly JsonUtils $jsonUtils
    ) {}

	/**
	 * @inheritDoc
	 */
	public function addBeans(int $numSpoons): void
	{
        $beanContainer = $this->getBeanContainerObject();

        $beanContainer->currentBeansSpoons += $numSpoons;
        if ($beanContainer->currentBeansSpoons > $beanContainer->maxCapacity) {
            throw new ContainerFullException();
        }

        $this->updateBeanContainerInfo($beanContainer);
	}

	/**
	 * @inheritDoc
	 */
	public function useBeans(int $numSpoons): int
	{
        $beanContainer = $this->getBeanContainerObject();

        $beanContainer->currentBeansSpoons -= $numSpoons;
        if ($beanContainer->currentBeansSpoons < 0) {
            return 0;
        }

        $this->updateBeanContainerInfo($beanContainer);
        return $numSpoons;
	}

	/**
	 * @inheritDoc
	 */
	public function getBeans(): int
	{
        $beanContainer = $this->getBeanContainerObject();
        return $beanContainer->currentBeansSpoons;
	}

    private function getBeanContainerObject(): stdClass
    {
        $data = $this->beanContainerGateway->getData();
        return $this->jsonUtils->jsonDecodeThrowsOnError($data);
    }

    private function updateBeanContainerInfo(stdClass $beanContainer): bool
    {
        $data = $this->jsonUtils->jsonEncodeThrowsOnError($beanContainer);
        return $this->beanContainerGateway->saveData($data);
    }
}
