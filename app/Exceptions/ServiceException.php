<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class ServiceException extends Exception
{
  protected $statusCode;

  public function __construct($message = "Action denied", $statusCode = 422, ?Throwable $previous = null)
  {
    $this->statusCode = $statusCode;
    parent::__construct($message, 0, $previous);
  }

  public function render($request)
  {
    return response()->json([
      'status' => 'error',
      'message' => $this->getMessage(),
      'error_type' => 'BUSINESS_RULE_VIOLATION'
    ], $this->statusCode);
  }
}
