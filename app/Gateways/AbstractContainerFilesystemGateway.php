<?php
declare(strict_types = 1);

namespace App\Gateways;

abstract class AbstractContainerFilesystemGateway implements ContainerGatewayInterface
{
    public function __construct(
        private readonly string $filename
    ) {}

    public function getData(): string
    {
        if (!file_exists($this->getInfoFilePath())) {
            return '';
        }

        return file_get_contents($this->getInfoFilePath());
    }

    public function saveData(string $jsonString): bool
    {
        if (!file_exists($this->getInfoFilePath())) {
            return false;
        }

        return file_put_contents($this->getInfoFilePath(), $jsonString) > 0;
    }

    private function getInfoFilePath(): string
    {
        return storage_path('data').DIRECTORY_SEPARATOR.$this->filename;
    }
}
