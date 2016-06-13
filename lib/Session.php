<?php

/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/6/12
 * Time: 12:49
 */

require_once ("../config/Config.php");

class Session
{
    private $_id;
    private $mongo;
    public function __construct($_id) {
        $this->_id = $_id;
        $this->mongo = new MongoDB\Driver\Manager(Config::$mongoAddr);
        $query = new MongoDB\Driver\Query(['_id' => $this->_id], ['projection' => ['_id' => 1]]);
        $cursor = $this->mongo->executeQuery(Config::$database . '.sessions', $query);
        $result = $cursor->toArray();
        if (count($result) == 0) return false;
    }

    public function getId() {
        return $this->_id;
    }

    public function getState() {
        $query = new MongoDB\Driver\Query(['_id' => $this->_id], ['projection' => ['state' => 1]]);
        $cursor = $this->mongo->executeQuery(Config::$database . '.sessions', $query);
        $result = $cursor->toArray();
        return $result[0]->state;
    }

    public function close() {
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->update(['_id' => $this->_id], ['$set' => ['state' => SESSION_STATE_CLOSE]], ['multi' => false, 'upsert' => false]);
        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        $this->mongo->executeBulkWrite(Config::$database . '.sessions', $bulk, $writeConcern);
    }

    public function getAddition() {
        $query = new MongoDB\Driver\Query(['_id' => $this->_id], ['projection' => ['addition' => 1]]);
        $cursor = $this->mongo->executeQuery(Config::$database . '.sessions', $query);
        $result = $cursor->toArray();
        return $result[0]->addition;
    }

    public function setWaiter($waiterID) {
        if ($this->getState() != SESSION_STATE_READY) return false;
        if ((new Waiter($waiterID)) == false) return false;
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->update(['_id' => $this->_id], ['$set' => ['state' => SESSION_STATE_OPEN, 'waiter_id' => $waiterID]], ['multi' => false, 'upsert' => false]);
        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        $this->mongo->executeBulkWrite(Config::$database . '.sessions', $bulk, $writeConcern);
        return true;
    }

    public function getNewestMessages($lastTime) {
        $query = new MongoDB\Driver\Query(['session_id' => $this->_id, 'create_time' => ['$gt' => $lastTime]],
            ['sort' => ['create_time' => -1]]);
        $cursor = $this->mongo->executeQuery(Config::$database . '.sessions', $query);
        $result = $cursor->toArray();
        return $result;
    }

    public function getLastMessages($num) {
        $query = new MongoDB\Driver\Query(['session_id' => $this->_id],
            ['sort' => ['create_time' => -1], 'limit' => intval($num)]);
        $cursor = $this->mongo->executeQuery(Config::$database . '.sessions', $query);
        $result = $cursor->toArray();
        return $result;
    }

    public function getUpdateTime() {
        $query = new MongoDB\Driver\Query(['_id' => $this->_id], ['projection' => ['update_time' => 1]]);
        $cursor = $this->mongo->executeQuery(Config::$database . '.sessions', $query);
        $result = $cursor->toArray();
        return $result[0]->update_time;
    }

    public function update() {
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->update(['_id' => $this->_id], ['$set' => ['update_time' => time()]], ['multi' => false, 'upsert' => false]);
        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        $this->mongo->executeBulkWrite(Config::$database . '.sessions', $bulk, $writeConcern);
    }

    public function getUserID() {
        $query = new MongoDB\Driver\Query(['_id' => $this->_id], ['projection' => ['user_id' => 1]]);
        $cursor = $this->mongo->executeQuery(Config::$database . '.sessions', $query);
        $result = $cursor->toArray();
        return $result[0]->user_id;
    }

    public function getWaiterID() {
        $query = new MongoDB\Driver\Query(['_id' => $this->_id], ['projection' => ['waiter_id' => 1]]);
        $cursor = $this->mongo->executeQuery(Config::$database . '.sessions', $query);
        $result = $cursor->toArray();
        return $result[0]->waiter_id;
    }

    public function getData() {
        $query = new MongoDB\Driver\Query(['_id' => $this->_id]);
        $cursor = $this->mongo->executeQuery(Config::$database . '.sessions', $query);
        $result = $cursor->toArray();
        return $result[0];
    }
}