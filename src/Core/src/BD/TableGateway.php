<?php
/**
 * Created by PhpStorm.
 * User: dsin
 * Date: 3/13/19
 * Time: 9:57 AM
 */

namespace Core\BD;


use Core\BD\PDO\Feature;
use Core\BD\PDO\ResultSet;
use Core\BD\PDO\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\Feature\EventFeatureEventsInterface;

class TableGateway  extends AbstractTableGateway
{
    private $_model;

    public function getModel(): Model
    {
        return $this->_model;
    }


    public function __construct(Model $model)
    {
        $this->_model = $model;

        $this->resultSetPrototype = new ResultSet(
            ResultSet::TYPE_ARRAYOBJECT, $this
        );

        $this->adapter = BD::getDBAdapter($model->getConnection());

        $model->setOptions(BD::getDBOptions($model->getConnection()));

        $this->table = $model->getTable();

        $this->sql = new Sql($this->adapter, $this->table);

        $this->initialize();

        $feature = new Feature($model);

        $this->featureSet->addFeature($feature);
    }

    public function insert($set)
    {
        if (! $this->isInitialized) {
            $this->initialize();
        }

        $this->sql->setReturning($this->_model->getPK());

        return parent::insert($set);
    }

    public function update($set, $where = null, array $joins = null)
    {
        if (! $this->isInitialized) {
            $this->initialize();
        }

        $this->sql->setReturning($this->_model->getPK());

        return parent::update($set, $where, $joins);
    }

    public function executeSQL($sql, $params = [])
    {
        // Prepara os parâmetros a serem inseridos
        $this->prepareParameters($sql, $params);

        // apply preSelect features
        $this->featureSet->apply(EventFeatureEventsInterface::EVENT_PRE_SELECT, [$sql]);

        // prepare and execute
        $statement = $this->sql->getAdapter()->query($sql);
        $result    = $statement->execute($params);

        // build result set
        $resultSet = clone $this->resultSetPrototype;
        $resultSet->initialize($result);

        // apply postSelect features
        $this->featureSet->apply(EventFeatureEventsInterface::EVENT_POST_SELECT, [$statement, $result, $resultSet]);

        // Retorna o resultset
        return $resultSet;
    }

    private function prepareParameters(&$sql, &$params)
    {
        // Inicializa o array de parâmetros
        $parameters = [];

        // Percorre os parâmetros
        foreach ($params as $param => $value) {
            // Monta a expressão regular para encontrar os parâmetros na SQL
            $regex = '/\b' . $param . '\b/u';

            // Verifica quantas vezes o parâmetro aparece na SQL
            $count = preg_match_all($regex, $sql);

            // Percorre o número de vezes que o parâmetro aparece na SQL
            for ($i = 0, $max = $count; $i < $max; $i++) {
                // Cria um novo parâmetro (Obs. Na execução da SQL não poderá haver dois parâmetros com o mesmo nome)
                $newParam = '_' . $param . '_' . $i . '_';
                // Substitui o antigo parâmetro pelo novo
                $sql = preg_replace($regex, $newParam, $sql, 1);
                // Verifica se o valor é um array
                if (is_array($value)) {
                    // Recupera a quantidade de campos no array
                    $countValues = count($value);
                    // Cria uma nova lista de parâmetros
                    $list = [];
                    // Percorre os valores do array
                    for ($j = 0, $maxValue = $countValues; $j < $maxValue; $j++) {
                        // Cria um novo elemento
                        $newElement = $newParam . $j . '_';
                        // Adiciona o elemento na lista de parâmetros
                        $list[] = ':' . $newElement;
                        // Salva no array de Parâmetros
                        $parameters[$newElement] = trim($value[$j]);
                    }
                    // Monta a expressão regular para encontrar o parâmetro a ser substituido
                    $regexIn = '/\B:' . $newParam . '\b/u';
                    // Substitui o parâmetro pelos da lista
                    $sql = preg_replace($regexIn, implode(',', $list), $sql, 1);
                    // Caso não seja array
                } else {
                    // Salva no array de Parâmetros
                    $parameters[$newParam] = $value;
                }
            }
        }

        // Salva os parâmetros na variável
        $params = $parameters;
    }

    public function exchangeArray(array $data)
    {
        $this->_model->clearData();
        $this->_model->setData($data);
    }
}