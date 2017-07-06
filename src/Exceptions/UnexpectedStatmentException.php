<?php 
namespace BrezgalovQueryBuilder\Exceptions;

class UnexpectedStatmentException extends Exception {
	public function __construct(
		$message = 'Specified statment order is not supported!';
		$code = 0, 
		Exception $previous = null
	) {
        parent::__construct($message, $code, $previous);
    }
}