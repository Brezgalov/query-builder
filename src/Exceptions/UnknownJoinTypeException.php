<?php 
namespace BrezgalovQueryBuilder\Exceptions;

class UnknownTypeException extends \Exception {
	public function __construct(
		$message = 'Specified type is unknown!';
		$code = 0, 
		Exception $previous = null
	) {
        parent::__construct($message, $code, $previous);
    }
}