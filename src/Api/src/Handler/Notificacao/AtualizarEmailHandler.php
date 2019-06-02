<?php

declare(strict_types=1);

namespace Api\Handler\Notificacao;

use Core\BD\BD;
use Core\Exception\MainException;
use Core\Handler\MainHandler;
use Core\Json\JsonException;
use Core\Json\JsonMessage;
use Core\Repository\Vendas\Config;
use Core\Repository\Vendas\Venda;
use Core\Util\MailUtil;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


class AtualizarEmailHandler extends MainHandler
{
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        try {
            BD::startTransaction('Vendas');

            $email = $this->getPost($request, 'email');

            if (!$email) {
                throw new MainException("Email não informado.");
            }

            Config::atualizarEmail($email);

            BD::commit('Vendas');

            return new JsonMessage($email);
        } catch (\Exception $ex) {
            BD::rollback('Dashboard');

            return new JsonException($ex);
        }
    }
}
