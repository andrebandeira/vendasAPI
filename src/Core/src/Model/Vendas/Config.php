<?php

namespace Core\Model\Vendas;

use Core\BD\Model;

class Config extends Model
{
    public function initialize()
    {
        $this->connection = 'Vendas';
        $this->table = 'CONFIG';
        $this->PK = ['ID'];
    }

    public static function find(Array $data){
        $model = new self();
        $model->setData($data);
        return $model->findOne();
    }

    public static function keyToken(){
        $model = new self();
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