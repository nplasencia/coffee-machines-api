<?php declare(strict_types = 1);

namespace App\Gateways;

interface ContainerGatewayInterface
{
    public function getData(): string;

    public function saveData(string $jsonString): bool;
}
