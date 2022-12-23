<?php

namespace app\exceptions;

use yii\db\Exception;

class OrderLoadException extends Exception
{
    public function __construct($message, $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
