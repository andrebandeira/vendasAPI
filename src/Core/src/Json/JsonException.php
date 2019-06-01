<?php

declare(strict_types=1);

namespace Core\Json;

use Core\Exception\DSINException;
use Core\Exception\MainException;
use Core\Model\Dashboard\DSIN;
use Core\Model\Dashboard\Excecao;
use Core\Util\MailUtil;
use Core\Util\TelegramUtil;
use Zend\Diactoros\Response\JsonResponse;


class JsonException extends JsonResponse
{
    public function __construct(
        \Exception $ex,
        $depto = null,
        $data = null)
    {
        if ($ex) {
            $msg = $this->_getMessage($ex, $depto, $data);
        }

        parent::__construct(
            ['error' => $msg],
            400
        );
    }

    private function _getMessage(\Exception $ex, $depto, $data)
    {
        $defaultMessage = 'Ocorreram erros no processamento da Informação. Entre em contato com o administrador do Sistema.';

        if ($ex instanceof MainException) {
            $msg = $ex->getMessage();
        } else {
            $env = strtolower(getenv('ENVIRONMENT') ?: 'PRODUCTION');

            if ($env == strtolower('DEVELOPMENT')) {
                $msg =  $ex->getMessage();
            } else {
                $msg =  $defaultMessage;
                $msg =  $ex->getMessage();
            }
        }

        return $msg;
    }
}