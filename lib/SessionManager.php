<?php
/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/6/12
 * Time: 12:39
 */

require_once ("../config/Config.php");
require_once ("Session.php");
require_once ("Waiter.php");
require_once ("common.php");


class SessionManager
{
    private $mongo;

    public function __construct() {
        $this->mongo = new MongoDB\Driver\Manager(Config::$mongoAddr);
    }

    private function newSessionWithoutCheckLast($userID, $waiterID, $addition) {
        $data = [
            'user_id' => intval($userID),
            'waiter_id' => intval($waiterID),
            'addition' => json_encode($addition),
            'state' => SESSION_STATE_OPEN,
            'create_time' => time(),
            'update_time' => time(),
        ];
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->insert($data);
        $this->mongo->executeBulkWrite(Config::$database . '.sessions', $bulk);
    }

    public function newSession($userID, $waiterID, $addition) {
        $lastSession = $this->getUserLastSession($userID);
        if ($lastSession) {
            $lastSession->Close();
        }
        $this->newSessionWithoutCheckLast($userID, $waiterID, $addition);
    }

    public function newReadySession($userID, $addition) {
        $data = [
            'user_id' => intval($userID),
            'addition' => $addition,
            'state' => SESSION_STATE_READY,
            'create_time' => time(),
            'update_time' => time(),
        ];
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->insert($data);
        $this->mongo->executeBulkWrite(Config::$database . '.sessions', $bulk);
    }

    public function getUserLastSession($userID) {
        $filter = ['user_id' => intval($userID), 'state' => ['$ne' => SESSION_STATE_CLOSE]];
        $options = ['projection' => ['_id' => 1], 'sort' => ['update_time' => -1]];
        $query = new MongoDB\Driver\Query($filter, $options);
        $result = $this->mongo->executeQuery(Config::$database . '.sessions', $query);
        $array = $result->toArray();
        produceOId($array);
        if (count($array) == 0) return false;
        return new Session($array[0]->_id);
    }

    public function getWaiterOpenSessions($waiterID) {
        return (new Waiter($waiterID))->getOpenSessions();
    }

    public function getReadySessions() {
        $filter = ['state' => SESSION_STATE_READY];
        $options = ['sort' => ['update_time' => -1]];
        $query = new MongoDB\Driver\Query($filter, $options);
        $result = $this->mongo->executeQuery(Config::$database . '.sessions', $query);
        $array = $result->toArray();
        produceOId($array);
        return $array;
    }
}