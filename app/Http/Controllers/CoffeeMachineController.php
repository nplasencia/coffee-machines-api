<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Services\EspressoMachineInterface;

class CoffeeMachineController extends Controller
{
    public function __construct(
        private readonly EspressoMachineInterface $espressoMachine
    ) {}

    public function makeEspresso(): JsonResponse
    {
        $this->espressoMachine->makeEspresso();
        return $this->getSuccessResponse('Enjoy your espresso');
    }

    public function makeDoubleEspresso(): JsonResponse
    {
        $this->espressoMachine->makeDoubleEspresso();
        return $this->getSuccessResponse('Enjoy your double espresso');
    }

    public function getMachineStatus(): JsonResponse
    {
        $status = $this->espressoMachine->getStatus();
        return $this->getSuccessResponse($status);
    }

    private function getSuccessResponse(string $message): JsonResponse
    {
        return response()->json(['status' => 'Success', 'message' => $message]);
    }
}
