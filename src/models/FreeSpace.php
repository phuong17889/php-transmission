<?php
namespace phuong17889\transmission\models;
use phuong17889\transmission\core\AbstractModel;

/**
 * @author Joysen Chellem
 */
class FreeSpace extends AbstractModel {

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @var integer
	 */
	private $size;

	/**
	 * Gets the value of path.
	 *
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * Sets the value of path.
	 *
	 * @param string $path the path
	 */
	public function setPath($path) {
		$this->path = $path;
	}

	/**
	 * Gets the value of size.
	 *
	 * @return integer
	 */
	public function getSize() {
		return $this->size;
	}

	/**
	 * Sets the value of size.
	 *
	 * @param integer $size the size
	 */
	public function setSize($size) {
		$this->size = $size;
	}

	/**
	 * @return array
	 */
	public static function getMapping() {
		return [
			'path'       => 'path',
			'size-bytes' => 'size',
		];
	}
}
