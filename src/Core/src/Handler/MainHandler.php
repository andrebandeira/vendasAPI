<?php

declare(strict_types=1);

namespace Core\Handler;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class MainHandler implements RequestHandlerInterface
{
    protected function getQuery(ServerRequestInterface $request, string $param){
        if (isset($request->getQueryParams()[$param])) {
            return $request->getQueryParams()[$param];
        }

        return null;
    }

    protected function getPost(ServerRequestInterface $request, string $param){
        if (isset($request->getParsedBody()[$param])) {
            return $request->getParsedBody()[$param];
        }

        return null;
    }

    protected function getServer(ServerRequestInterface $request, string $param){
        if (isset($request->getServerParams()[$param])) {
            return $request->getServerParams()[$param];
        }

        return null;
    }

    protected function getParam(ServerRequestInterface $request, string $param){
        $parameter = $this->getPost($request, $param);

        if ($parameter) {
            return $parameter;
        }

        $parameter = $this->getQuery($request, $param);

        if ($parameter) {
            return $parameter;
        }

        $parameter = $this->getServer($request, $param);

        if ($parameter) {
            return $parameter;
        }

        return null;
    }
}
