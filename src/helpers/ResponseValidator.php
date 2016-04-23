<?php
namespace phuong17889\transmission\helpers;

use RuntimeException;
use phuong17889\transmission\core\Object;

/**
 * @author Ramon Kleiss <ramon@cubilon.nl>
 */
class ResponseValidator {

	/**
	 * @param          $method
	 * @param Object   $response
	 *
	 * @return Object|array
	 */
	public static function validate($method, Object $response) {
		if (!isset($response->result)) {
			throw new RuntimeException('Invalid response received from Transmission');
		}
		if (!in_array($response->result, array(
			'success',
			'duplicate torrent',
		))
		) {
			throw new RuntimeException(sprintf('An error occured: "%s"', $response->result));
		}
		switch ($method) {
			case 'torrent-get':
				return self::validateGetResponse($response);
			case 'torrent-add':
				return self::validateAddResponse($response);
			case 'session-get':
				return self::validateSessionGetResponse($response);
			case 'session-stats':
				return self::validateSessionStatsGetResponse($response);
			case 'free-space':
				return self::validateFreeSpaceGetResponse($response);
			default:
				return self::validateGetResponse($response);
		}
	}

	/**
	 * @param  Object $response
	 *
	 * @throws RuntimeException
	 */
	public static function validateGetResponse(Object $response) {
		if (!isset($response->arguments) || !isset($response->arguments->torrents)) {
			throw new RuntimeException('Invalid response received from Transmission');
		}
		return $response->arguments->torrents;
	}

	/**
	 * @param  Object $response
	 *
	 * @throws RuntimeException
	 */
	public static function validateAddResponse(Object $response) {
		$fields = array(
			'torrent-added',
			'torrent-duplicate',
		);
		foreach ($fields as $field) {
			if (isset($response->arguments) && isset($response->arguments->$field) && count($response->arguments->$field)) {
				return $response->arguments->$field;
			}
		}
		throw new RuntimeException('Invalid response received from Transmission');
	}

	public static function validateSessionGetResponse(Object $response) {
		if (!isset($response->arguments)) {
			throw new RuntimeException('Invalid response received from Transmission');
		}
		return $response->arguments;
	}

	/**
	 * @param  Object $response
	 *
	 * @return Object
	 */
	public static function validateSessionStatsGetResponse(Object $response) {
		if (!isset($response->arguments)) {
			throw new RuntimeException('Invalid response received from Transmission');
		}
		$class = 'Transmission\\Model\\Stats\\Stats';
		foreach (
			array(
				'cumulative-stats',
				'current-stats',
			) as $property
		) {
			if (property_exists($response->arguments, $property)) {
				$instance                       = self::map($response->arguments->$property, $class);
				$response->arguments->$property = $instance;
			}
		}
		return $response->arguments;
	}

	private static function map($object, $class) {
		return PropertyMapper::map(new $class(), $object);
	}

	public static function validateFreeSpaceGetResponse(Object $response) {
		if (!isset($response->arguments)) {
			throw new RuntimeException('Invalid response received from Transmission');
		}
		return $response->arguments;
	}
}
