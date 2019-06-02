<?php


namespace Core\Repository\Vendas;


use Core\BD\Repository;
use Core\Exception\MainException;

class Config extends Repository
{
    public static function keyToken(){
        $model = self::getModel();

        $model->ID = 1;
        $registro = $model->findOne();

        if ($registro) {
            $keyToken = $registro->KEY_TOKEN;
            if ($keyToken) {
                return $keyToken;
            }
        }

        return '574249d46a141f2a823c5e43e8e2e957';
    }

    public static function email(){
        $model = self::getModel();

        $model->ID = 1;
        $registro = $model->findOne();

        if ($registro) {
            $keyToken = $registro->EMAIL_RELATORIO;
            if ($keyToken) {
                return $keyToken;
            }
        }

        return null;
    }

    public static function atualizarEmail($email){
        $model = self::getModel();

        $model->ID = 1;
        $registro = $model->findOne();

        if ($registro) {
            $registro->EMAIL_RELATORIO = $email;
            $registro->update();
        } else {
            throw new MainException("Registro de Configuração não Encontrado");
        }
    }
}