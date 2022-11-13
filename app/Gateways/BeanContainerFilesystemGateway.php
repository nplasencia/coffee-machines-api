<?php declare(strict_types = 1);

namespace App\Gateways;

class BeanContainerFilesystemGateway extends AbstractContainerFilesystemGateway
{
    private const BEANS_INFO_FILENAME = 'beans_container.json';

    public function __construct()
    {
        parent::__construct(self::BEANS_INFO_FILENAME);
    }
}
