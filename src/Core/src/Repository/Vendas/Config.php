<?php


namespace Core\Repository\Vendas;


use Core\BD\Repository;

class Config extends Repository
{
    public static function keyToken(){
        $model = self::getModel();

        $resultBusca = $model->all();

        $registro = $resultBusca->current();

        if ($registro) {
            $keyToken = $registro->KEY_TOKEN;
            if ($keyToken) {
                return $keyToken;
            }
        }

        return '574249d46a141f2a823c5e43e8e2e957';
    }
}