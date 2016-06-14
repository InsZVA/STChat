<?php

/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/6/12
 * Time: 18:12
 */

require_once ('../config/Config.php');

class User
{
    private $_id;
    private $mongo;
    public function __construct($_id) {
        $this->_id = $_id;
        $this->mongo = new MongoDB\Driver\Manager(Config::$mongoAddr);
        if (!$this->verify()) return false;
    }

    public function getHistorySessions($offset, $num) {
        $query = new MongoDB\Driver\Query(['user_id' => $this->_id],
            ['sort' => ['create_time' => -1], 'limit' => intval($num), 'skip' => intval($offset)]);
        $cursor = $this->mongo->executeQuery(Config::$database . '.sessions', $query);
        $result = $cursor->toArray();
        produceOId($result);
        return $result;
    }

    public function verify() {
        //TODO: 确认用户id合法性
        return true;
    }

    public function sendMessage($sessionId, $type, $content) {
        $mm = new MessageManager();
        $session = new Session($sessionId);
        if (!$session) return false;
        if ($session->getUserID() != $this->_id) return false;
        return $mm->newMessage($sessionId, SESSION_SENDER_USER, $type, $content);
    }
}