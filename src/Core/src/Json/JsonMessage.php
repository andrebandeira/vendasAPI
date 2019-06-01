<?php

declare(strict_types=1);

namespace Core\Json;

use Zend\Diactoros\Response\JsonResponse;

class JsonMessage extends JsonResponse
{
    public function __construct($data = "")
    {
        parent::__construct(['data' => $data], 200);
    }
}