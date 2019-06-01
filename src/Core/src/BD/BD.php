<?php

declare(strict_types=1);

namespace Core\BD;

use Core\Model\Dashboard\Departamento;
use Zend\Db\Adapter\Adapter;

class BD
{
    private static $_transactions;
    private static $_dbsConfig;
    private static $_dbsAdapter;
    private static $_dbsOptions;

    public static function getDBAdapter($dbName)
    {
        if (!self::$_dbsConfig) {
            $container = require 'config/container.php';
            self::$_dbsConfig = $container->get('config')['dbs'];
            self::$_dbsAdapter = [];
        }

        if (!isset(self::$_dbsAdapter[$dbName])) {
            if (!$db = self::$_dbsConfig[$dbName]) {
                throw new \Exception(
                    'Não foi possível encontrar a conexão com: ' . $dbName
                );
            }

            $connection             = [];
            $connection['driver']   = $db['driver'];
            $connection['dsn']      = $db['dsn'];
            $connection['username'] = $db['username'];
            $connection['password'] = $db['password'];

            self::$_dbsAdapter[$dbName] = new Adapter($connection);

            if (isset($db['options'])) {
                self::$_dbsOptions[$dbName] = $db['options'];
            }
        }

        return self::$_dbsAdapter[$dbName];
    }

    public static function getDBOptions($dbName){
        if (isset(self::$_dbsOptions[$dbName])) {
            return self::$_dbsOptions[$dbName];
        }
        return null;
    }

    public static function getDBConfig(){
        return self::$_dbsConfig;
    }

    public static function setDBConfig($dbsConfig){
        self::$_dbsConfig = $dbsConfig;
    }

    public static function removeAdapter($dbName){
        unset(self::$_dbsAdapter[$dbName]);
    }

    public static function setUsuarioDB($departamento){
        $bdsConfig = self::getDBConfig();

        $usuario = $bdsConfig['Dashboard']['username'];

        $usuario = explode('_', $usuario);
        $usuario = $usuario[0];

        $bdsConfig['Dashboard']['username'] = $usuario.'_'.$departamento;

        self::removeAdapter('Dashboard');
        self::setDBConfig($bdsConfig);
    }

    public static function startTransaction($dbName)
    {
        $dbAdapter = self::getDBAdapter($dbName);

        $connection =  $dbAdapter->getDriver()->getConnection();

        $transaction = $connection->beginTransaction();

        if ($transaction) {
            if (!isset(self::$_transactions[$dbName])) {
                self::$_transactions[$dbName] = [];
            }

            array_push(
                self::$_transactions[$dbName],
                $transaction
            );
        }
    }

    public static function commit($dbName)
    {
        if (isset(self::$_transactions[$dbName])) {
            if ($connection = array_pop(self::$_transactions[$dbName])) {

                $connection->commit();
            }
        }
    }

    public static function rollback($dbName)
    {
        if (isset(self::$_transactions[$dbName])) {
            if ($connection =  array_pop(self::$_transactions[$dbName])) {

                $connection->rollback();
            }
        }
    }
}
