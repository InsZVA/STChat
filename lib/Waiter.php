<?php

/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/6/12
 * Time: 15:27
 */

require_once ('../config/Config.php');
require_once ("MessageManager.php");

class Waiter
{
    private $_id;
    private $mongo;
    public function __construct($_id) {
        $this->_id = $_id;
        $this->mongo = new MongoDB\Driver\Manager(Config::$mongoAddr);
        $query = new MongoDB\Driver\Query(['_id' => $this->_id], ['projection' => ['_id' => 1]]);
        $cursor = $this->mongo->executeQuery(Config::$database . '.waiters', $query);
        $result = $cursor->toArray();
        if (count($result) == 0) throw new Exception("unknown waiter id");
    }

    public function setImage($url) {
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->update(['_id' => $this->_id], ['$set' => ['image_url' => $url]], ['multi' => false, 'upsert' => false]);
        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        $this->mongo->executeBulkWrite(Config::$database . '.waiters', $bulk, $writeConcern);
    }

    public function delete() {
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->delete(['_id' => $this->_id], ['limit' => 1]);
        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        $this->mongo->executeBulkWrite(Config::$database . '.waiters', $bulk, $writeConcern);
    }

    public function getOpenSessions() {
        $filter = ['waiter_id' => intval($this->_id), 'state' => SESSION_STATE_OPEN];
        $options = ['sort' => ['update_time' => -1]];
        $query = new MongoDB\Driver\Query($filter, $options);
        $result = $this->mongo->executeQuery(Config::$database . '.sessions', $query);
        $array = $result->toArray();
        produceOId($array);
        return $array;
    }

    public function sendMessage($sessionId, $type, $content) {
        $mm = new MessageManager();
        $session = new Session($sessionId);
        if (!$session) return false;
        if ($session->getWaiterID() != $this->_id) return false;
        return $mm->newMessage($sessionId, SESSION_SENDER_WAITER, $type, $content);
    }

    public function getData() {
        $query = new MongoDB\Driver\Query(['_id' => $this->_id]);
        $cursor = $this->mongo->executeQuery(Config::$database . '.waiters', $query);
        $result = $cursor->toArray();
        produceOId($result);
        return $result[0];
    }
}