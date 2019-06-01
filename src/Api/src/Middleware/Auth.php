<?php

declare(strict_types=1);

namespace Api\Middleware;

use Core\BD\BD;
use Core\Exception\MainException;
use Core\Json\JsonException;
use Core\Repository\Vendas\Config;
use Core\Repository\Vendas\Usuario;
use Core\Util\LogUtil;
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Auth implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            BD::startTransaction('Vendas');

            $token = $this->getHeader($request, 'token');

            if (!$token) {
                throw new MainException(
                    'Atenção. Token não informado. Faça o Login Novamente!'
                );
            }

            $key = Config::keyToken();

            try {
                $decoded = JWT::decode($token, $key, ['HS256']);
            } catch (\Exception $ex) {
                throw new MainException('Atenção. Token Inválido. Faça o Login Novamente!');
            }

            $idUsuario = $decoded->USUARIO;
            $ip = $decoded->IP;

            if ($ip != LogUtil::getRealIpAddr()) {
                throw new MainException(
                    'Atenção. Você esta logado em outro dispositivo. Faça o Login Novamente!'
                );
            }

            $usuario = Usuario::find(
                [
                    'ID' => $idUsuario
                ]
            );

            if ($usuario) {
                $tokenDB = $usuario->AUTH_TOKEN;

                if ($tokenDB != $token) {
                    throw new MainException('Atenção. Token Inválido. Faça o Login Novamente!');
                }

                $date = new \DateTime($usuario->AUTH_VALIDADE);

                if ($date->getTimestamp() < time()) {
                    throw new MainException('Atenção. Login Expirado por Inatividade. Faça o Login Novamente!');
                }

                $date = new \DateTime();
                $date->setTimestamp(time() + 3600);
                $usuario->AUTH_VALIDADE = $date->format('Y-m-d H:i:s');

                $usuario->update();

                LogUtil::setUser($usuario->ID);

                BD::commit('Vendas');

                return $handler->handle($request);
            }

            throw new MainException('Atenção. Token Inválido. Faça o Login Novamente!');

        } catch (\Exception $ex) {
            BD::rollback('Vendas');

            return new JsonException($ex);
        }
    }

    private function getHeader(ServerRequestInterface $request, string $param)
    {
        if (isset($request->getHeaders()[$param])) {
            return $request->getHeaders()[$param][0];
        }

        return null;
    }
}