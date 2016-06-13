<?php

/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/6/12
 * Time: 17:28
 */

require_once ('../config/Config.php');
require_once ('Session.php');

class MessageManager
{
    private $mongo;

    public function __construct() {
        $this->mongo = new MongoDB\Driver\Manager(Config::$mongoAddr);
    }

    public function newMessage($sessionID, $sender, $type, $content) {
        $session = new Session($sessionID);
        if ($session->getState() == SESSION_STATE_CLOSE) return false;
        $userID = $session->getUserID();
        $waiterID = $session->getWaiterID();
        $data = [
            'session_id' => intval($sessionID),
            'user_id' => intval($userID),
            'waiter_id' => intval($waiterID),
            'type' => $type,
            'sender' => $sender,
            'content' => $content,
            'create_time' => time(),
        ];
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->insert($data);
        $this->mongo->executeBulkWrite(Config::$database . '.messages', $bulk);
        (new Session($sessionID))->update();
        return true;
    }
}