<?php

namespace Crumbls\Importer\Services;

use Closure;
use Crumbls\Importer\Drivers\Csv;
use Crumbls\Importer\Exceptions\UnknownEncoding;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Manager;
use InvalidArgumentException;

class ImportManager extends Manager
{
	public function __construct(Container $container) {
		parent::__construct($container);
		$this->config = (array)$this->config->get('importer');
		$this->initializeDrivers();
	}


	/**
	 * Get all of the created "drivers".
	 *
	 * @return array
	 */
	public function getDrivers()
	{
		return $this->customCreators;
	}

	public function initializeDrivers() : void {
		if (!array_key_exists('drivers', $this->config) || !is_array($this->config['drivers'])) {
			throw new \Exception(__METHOD__);
		}
		foreach($this->config['drivers'] as $key => $class) {
			$this->extend($key, function() use ($class) {
				return new $class($this->container);
			});
		}
	}

	/**
	 * Get the default driver name.
	 *
	 * @return string
	 */
	public function getDefaultDriver()
	{
		if (!array_key_exists('driver_default', $this->config)) {
			throw new \Exception(__METHOD__);
		}
		return $this->config['driver_default'];
	}

	/**
	 * @param $filepath
	 * @return string|null
	 * @throws UnknownEncoding
	 */
	public static function detectFileEncoding($filepath) : string|null {
		$output = [];
		exec('file -i ' . $filepath, $output);
		if (isset($output[0])){
			$ex = explode('charset=', $output[0]);
			return isset($ex[1]) ? $ex[1] : null;
		}
		throw new UnknownEncoding();
	}


	/**
	 * Register a custom driver creator Closure.
	 *
	 * @param  string  $driver
	 * @param  \Closure  $callback
	 * @return $this
	 */
	public function extend($driver, Closure $callback)
	{
		$this->customCreators[$driver] = $callback;

		return $this;
	}

	public function getSupportedMimeTypes() : array {

		$mimeTypes = array_merge(
			call_user_func_array('array_merge',
				array_map(function ($driver) {
					return $this->callDriver($driver)->getSupportedMimeTypes();
				}, array_keys($this->drivers)
				)
			),
			call_user_func_array('array_merge',
				array_map(function ($driver) {
					return $this->callCustomCreator($driver)->getSupportedMimeTypes();
				}, array_keys($this->customCreators)
				)
			)
		);
		$mimeTypes = array_unique($mimeTypes);
		sort($mimeTypes);
		return $mimeTypes;
	}
}