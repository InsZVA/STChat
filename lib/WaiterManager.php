<?php

/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/6/12
 * Time: 11:35
 */

class WaiterManager
{
    public function __construct() {
        $this->mongo = new MongoDB\Driver\Manager(Config::$mongoAddr);
    }

    public function newWaiter($data) {
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->insert($data);
        $this->mongo->executeBulkWrite(Config::$database . '.waiters', $bulk);
    }

    public function getWaiterList() {
        $query = new MongoDB\Driver\Query([]);
        $cursor = $this->mongo->executeQuery(Config::$database . '.waiters', $query);
        $result = $cursor->toArray();
        return $result;
    }
}