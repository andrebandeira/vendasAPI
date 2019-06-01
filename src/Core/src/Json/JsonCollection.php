<?php

declare(strict_types=1);

namespace Core\Json;

use Zend\Diactoros\Response\JsonResponse;

class JsonCollection extends JsonResponse
{
    public function __construct(
        $rows,
        $recordsTotal = 0,
        $startIndex = 0,
        $sort = '',
        $dir = 'ASC',
        $pageSize = 10
    ) {
        $numRegistros = count($rows);

        $data = [
            'data' => $rows,
            'recordsFiltered' => $numRegistros,
            'recordsTotal' => $recordsTotal ? $recordsTotal : $numRegistros,
            'startIndex' => $startIndex,
            'sort' => $sort,
            'dir' => $dir,
            'pageSize' => $pageSize
        ];

        parent::__construct($data, 200);
    }
}