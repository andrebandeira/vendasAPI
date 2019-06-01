<?php

declare(strict_types=1);

namespace Core\BD;

abstract class Repository
{
    public static function find(Array $data){
        $model = self::getModel();
        $model->setData($data);
        return $model->findOne();
    }

    public static function getAll(){
        $model = self::getModel();
        return $model->all();
    }

    protected static function getModel() : Model{
        $model = str_replace('Repository', 'Model', static::class);
        $model = new $model();
        if(!$model) {
            throw new \Exception('O repository não encontrou o model relacionado');
        }
        return $model;
    }


}
