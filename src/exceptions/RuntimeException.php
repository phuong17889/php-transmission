<?php
namespace phuong17889\transmission\exceptions;

use Exception;

/**
 * Created by Navatech.
 * @project transmission-php
 * @author  Phuong
 * @email   phuong17889[at]gmail.com
 * @date    4/23/2016
 * @time    12:48 AM
 */
class RuntimeException extends \RuntimeException {

	public function __construct($message = "", $code = 0, Exception $previous = null) {
		$message .= "<br/> You should start again";
		return parent::__construct($message, $code, $previous);
	}
}