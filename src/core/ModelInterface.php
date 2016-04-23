<?php
namespace phuong17889\transmission\core;
/**
 * The interface Transmission models must implement
 *
 * @author Ramon Kleiss <ramon@cubilon.nl>
 */
interface ModelInterface {

	/**
	 * Get the mapping of the model
	 *
	 * @return array
	 */
	public static function getMapping();
}
