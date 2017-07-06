<?php 
namespace BrezgalovQueryBuilder\Exceptions;

class OverwritingConditionException extends \Exception {
	public function __construct(
		$message = 'Attempt to overwrite condition detected!',
		$code = 0, 
		Exception $previous = null
	) {
        parent::__construct($message, $code, $previous);
    }
}