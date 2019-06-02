<?php

declare(strict_types=1);

namespace Auth\Handler;

use Core\BD\BD;
use Core\Exception\MainException;
use Core\Handler\MainHandler;
use Core\Json\JsonException;
use Core\Json\JsonMessage;
use Core\Repository\Vendas\Config;
use Core\Repository\Vendas\Usuario;
use Core\Util\LogUtil;
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TokenHandler extends MainHandler
{
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        try {
            BD::startTransaction('Vendas');

            $email = $this->getPost($request, 'email');
            $senha = $this->getPost($request, 'senha');

            if ($email && $senha) {
                $usuario = Usuario::find(
                    [
                        'EMAIL' => $email,
                    ]
                );

                if ($usuario) {
                    $senhaBD = $usuario->SENHA;

                    if (password_verify($senha, $senhaBD)) {
                        $token = [
                            'USUARIO'  => $usuario->ID,
                            'IP'       => LogUtil::getRealIpAddr(),
                            'time'     => time()
                        ];

                        $key = Config::keyToken();

                        $jwt = JWT::encode($token, $key);

                        $usuario->AUTH_TOKEN = $jwt;

                        $date = new \DateTime();
                        $date->setTimestamp(time() + 3600);
                        $usuario->AUTH_VALIDADE = $date->format('Y-m-d H:i:s');

                        $usuario->update();

                        LogUtil::setUser($usuario->ID);

                        BD::commit('Vendas');

                        return new JsonMessage($jwt);
                    }
                }
            }

            throw new MainException(
                'Atenção. Credenciais Inválidas. Verifique!!!'
            );

        } catch (\Exception $ex) {
            BD::rollback('Vendas');

            return new JsonException($ex);
        }
    }
}
