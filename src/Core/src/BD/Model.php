<?php

declare(strict_types=1);

namespace Core\BD;

use Core\BD\PDO\ResultSet;
use Core\Exception\MainException;
use Matrix\Exception;
use Zend\Db\Sql\TableIdentifier;

abstract class Model
{
    protected $connection;
    protected $table;
    protected $PK;
    protected $SK;
    protected $schema;
    protected $data;

    private $tableGateway;
    private $_options;

    protected $relations;

    public function __construct($connection = null)
    {
        $this->data = [];
        $this->_options = [];

        $this->initialize();

        if ($connection) {
            $this->connection = $connection;
        }

        if (!$this->connection) {
            throw new Exception('Connection não informada ao model');
        }

        if (!$this->table) {
            throw new Exception('Table não informada ao model');
        }

        if (!$this->PK) {
            throw new Exception('PK não informada ao model');
        }

        if (!$this->SK || !count($this->SK)){
            $this->SK = $this->PK;
        }

        $this->tableGateway = new TableGateway($this);
    }

    protected abstract function initialize();

    public function getConnection() {
        return $this->connection;
    }

    public function getTable() {
        $schema = $this->getSchema();

        $table = $this->table;

        if ($this->getOption('LOWER_CASE')) {
            if ($schema) {
                $schema = mb_strtolower(strval($schema));
            }
            $table = mb_strtolower($table);
        }

        return new TableIdentifier(
            $table,
            $schema
        );
    }

    public function getSchema() {
        return $this->schema;
    }

    public function setSchema($schema) {
        $this->schema = $schema;
    }

    public function getPK() {
        return $this->PK;
    }

    public function getSK(){
        return $this->SK;
    }

    public function getPKData() {
        $pkData = array();
        foreach ($this->PK as $value) {
            if (array_key_exists($value, $this->data)) {
                $pkData[$value] = $this->data[$value];
            }
        }

        if (count($pkData) != count($this->PK)) {
            $pkData = $this->getSKData();

            if (count($pkData) != count($this->SK)) {
                throw new \Exception(
                    "Não foi informada a PK ou SK completa"
                );
            }
        }

        if ($this->getOption('LOWER_CASE')) {
            return array_change_key_case($pkData, CASE_LOWER);
        }

        return $pkData;
    }

    private function getSKData() {
        $skData = array();
        foreach ($this->SK as $value) {
            if (array_key_exists($value, $this->data)) {
                $skData[$value] = $this->data[$value];
            }
        }

        return $skData;
    }

    public function getData() {
        if ($this->getOption('LOWER_CASE')) {
            return array_change_key_case ($this->data, CASE_LOWER);
        }

        return $this->data;
    }

    public function toArray() {
        return $this->data;
    }

    public function clearData() {
        $this->data = [];
    }

    public function setData($data) {
        if ($data) {
            foreach ($data as $key => $value) {
                $this->$key = $value;
            }
        }
    }

    public function findOne()
    {
        $keys = $this->getPKData();

        $rowset = $this->tableGateway->select($keys);

        $row = $rowset->current();

        if ($row) {
            return $this;
        }

        return false;
    }

    /**
     * @return ResultSet
     */
    public function findAll()
    {
        $data = $this->getData();

        return $this->tableGateway->select($data);
    }


    public function all()
    {
        $rowset = $this->tableGateway->select();

        return $rowset;
    }

    public function insert()
    {
        $data = $this->getData();

        if ($this->tableGateway->insert($data) < 1) {
            throw new \Exception("Erro ao inserir o registro");
        }

        return true;
    }

    public function update()
    {
        $data = $this->getData();

        $keys = $this->getPKData();

        if ($this->tableGateway->update($data, $keys) < 1) {
            throw new \Exception("Registro não encontrado. Verifique!!!");
        }

        return true;
    }

    public function upsert()
    {
        $keys = $this->getPKData();

        $data = $this->getData();

        if ($this->tableGateway->update($data, $keys) > 0) {
            return true;
        }

        if ($this->tableGateway->insert($data) > 0) {
            return true;
        }


        throw new \Exception("Erro ao inserir/atualizar o registro");
    }

    public function delete()
    {
        $keys = $this->getPKData();

        if ($this->tableGateway->delete($keys) < 1) {
            throw new \Exception("Registro não encontrado. Verifique!!!");
        }

        return true;
    }

    public function execute(String $sql, array $params = [])
    {
        $table = $this->table;

        $schema = $this->schema;

        if ($schema) {
            $table = $schema.'.'.$table;
        }

        $sql = str_replace(
            ':TABLE', $table, $sql
        );

        return $this->tableGateway->executeSQL($sql, $params);
    }

    public function setOptions($options)
    {
        $this->_options = $options;
    }

    public function getOption(String $option)
    {
        if (isset($this->_options[$option]))  {
            return $this->_options[$option];
        }

        return null;
    }

    public function __set($key, $value) {
        if (is_string($key)) {
            $key = trim($key);
            $key = mb_strtoupper($key);
        }

        if (is_string($value)) {
            $value = trim($value);
        }

        $this->data[$key] = $value;
    }

    public function __get($key) {
        if (is_string($key)) {
            $key = trim($key);
            $key = mb_strtoupper($key);
        }

        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        if (method_exists($this, $key)) {
            return $this->$key();
        }

        throw new \Exception('Campo '.$key.' não encontrado');
    }

    public function getRelationship(
        Model $model, array $references, bool $forcedQuery = false,
        bool $storeQuery = true
    ) {
        foreach ($references as $column => $reference) {
            $value = $this->$column;
            if (is_null($value)) {
                return [];
            } else {
                $model->$reference = $value;
            }
        }

        $relationshipId = Model::getRelationshipID(
            $this->table,
            $model->table,
            array_keys($references),
            array_values($references),
            array_values($model->data)
        );

        $relationship = $this->relations[$relationshipId] ?? null;


        if ($relationship && !$forcedQuery) {
            return $relationship;
        }



        $resultBusca = $model->findAll();

        $relationship = $resultBusca->toArray();

        if (count($relationship) == 1)  {
            $relationship = $relationship[0];
        }

        if ($storeQuery) {
            $this->relations[$relationshipId] = $relationship;
        }

        return $relationship;
    }

    private static function getRelationshipID(
        $sourceTable, $relationTable, $sourceFields, $relationFields, $data
    ) {
        $sourceFields = implode('|', $sourceFields);
        $relationFields = implode('|', $relationFields);
        $data = implode('|', $data);

        return  $sourceTable.'|'. $relationTable .'|'.$sourceFields.'|'.$relationFields.'|'.$data;
    }
}
