<?php
namespace phuong17889\transmission\core;

use phuong17889\transmission\Client;
use phuong17889\transmission\helpers\ResponseValidator;

/**
 * Base class for Transmission models
 *
 * @author Ramon Kleiss <ramon@cubilon.nl>
 */
abstract class AbstractModel implements ModelInterface {

	/**
	 * @var Client
	 */
	protected $client;

	/**
	 * Constructor
	 *
	 * @param Client $client
	 */
	public function __construct($client = null) {
		$this->client = $client;
	}

	/**
	 * @param Client $client
	 */
	public function setClient(Client $client) {
		$this->client = $client;
	}

	/**
	 * @return Client
	 */
	public function getClient() {
		return $this->client;
	}

	/**
	 * @return array
	 */
	public static function getMapping() {
		return [];
	}

	/**
	 * Returns the fully qualified name of this class.
	 * @return string the fully qualified name of this class.
	 */
	public static function className() {
		return get_called_class();
	}

	/**
	 * @param string $method
	 * @param array  $arguments
	 */
	protected function call($method, $arguments) {
		if ($this->client) {
			ResponseValidator::validate($method, $this->client->call($method, $arguments));
		}
	}
}
