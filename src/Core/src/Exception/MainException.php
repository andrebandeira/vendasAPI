<?php

declare(strict_types=1);

namespace Core\Exception;


class MainException extends \Exception
{
    public function __construct($msg, $codigo = 0, $previous = null)
    {
        parent::__construct($msg, $codigo, $previous);
    }
}