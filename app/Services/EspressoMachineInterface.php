<?php

namespace App\Services;

use App\Exceptions\NoBeansException;
use SoConnect\Coffee\NoWaterException;

/**
 * A single espresso uses 1 spoon of beans and 0.05 litres of water
 * A double espresso uses 2 spoons of beans and 0.10 litres of water
 */
interface EspressoMachineInterface
{
	/**
	 * Runs the process for making Espresso
	 *
	 * @return float amount of litres of coffee made
	 *
	 * @throws NoBeansException
	 * @throws NoWaterException
	 */
	public function makeEspresso(): float;

	/**
	 * Runs the process for making Double Espresso
	 *
	 * @return float of litres of coffee made
	 *
	 * @throws NoBeansException
	 * @throws NoWaterException
	 */
	public function makeDoubleEspresso(): float;

	/**
	 * This method controls what is displayed on the screen of the machine
	 * Returns ONE of the following human readable statuses in the following preference order:
	 *
	 * - Add beans and water
	 * - Add beans
	 * - Add water
	 * - {int} Espressos left
	 *
	 * @return string
	 */
	public function getStatus(): string;
}
