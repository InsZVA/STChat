<?php
/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/6/12
 * Time: 18:30
 */

/*
 * Method: POST
 * Content-Type: Json/Application
 */

require_once ('../lib/accessControl.php');
require_once ('../lib/Waiter.php');
require_once ('../lib/WaiterManager.php');
require_once ('../lib/SessionManager.php');
require_once ('../lib/User.php');
require_once ('../lib/common.php');

function OKResponse() {
    echo '{"code":0, "msg":"success"}';
    exit(0);
}


$rawPost = file_get_contents('php://input');
$postData = json_decode($rawPost);

switch($postData->requestMethod) {
    case "getWaiterList":
        if (!checkControlLevel3($postData)) break;
        $wm = new WaiterManager();
        $result = $wm->getWaiterList();
        echo json_encode($result);
        exit(0);
    case "waiterSendMessage":
        if (!checkControlLevel2($postData)) break;   //Level2 给与一定信任
        if (isset($postData->waiterId) && isset($postData->sessionId) && isset($postData->type) && isset($postData->content)) {
            $waiter = new Waiter($postData->waiterId);
            if (!$waiter) break;
            if (!$waiter->sendMessage($postData->sessionId, $postData->type, $postData->content))break;
            OKResponse();
        }
        break;
    case "newWaiter":
        if (!checkControlLevel3($postData)) break;
        if (isset($postData->data)) {
            (new WaiterManager())->newWaiter($postData->data);
            OKResponse();
        }
        break;
    case "deleteWaiter":
        if (!checkControlLevel3($postData)) break;
        if (isset($postData->waiterId)) {
            $waiter = new Waiter($postData->waiterId);
            if (!$waiter) break;
            $waiter->delete();
            OKResponse();
        }
        break;
    case "newSession":
        if (!checkControlLevel1($postData)) break;
        if (isset($postData->userId) && isset($postData->addition)) {
            $sm = new SessionManager();
            $sm->newReadySession($postData->userId, $postData->addition);
            OKResponse();
        }
        break;
    case "getUserHistorySessions":
        if (!checkControlLevel1($postData)) break;
        if (isset($postData->userId)) {
            if (!isset($postData->num)) $postData->num = 20;
            if (!isset($postData->offset)) $postData->offset = 0;
            $user = new User($postData->userId);
            if (!$user) break;
            $result = $user->getHistorySessions($postData->offset, $postData->num);
            echo json_encode($result);
            exit(0);
        }
        break;
    case "getWaiterOpenSessions":
        if (!checkControlLevel2($postData)) break;
        if (isset($postData->waiterId)) {
            $waiter = new Waiter($postData->waiterId);
            if (!$waiter) break;
            $result = $waiter->getOpenSessions();
            echo json_encode($result);
            exit(0);
        }
        break;
    case "getSessionData":
        if (!checkControlLevel2($postData)) break;
        if (isset($postData->sessionId)) {
            $session = new Session($postData->sessionId);
            if (!$session) break;
            $result = $session->getData();
            echo json_encode($result);
            exit(0);
        }
        break;
    case "closeSession":
        if (!checkControlLevel2($postData)) break;
        if (isset($postData->sessionId)) {
            $session = new Session($postData->sessionId);
            if (!$session) break;
            $session->close();
            OKResponse();
        }
        break;
    case "userSendMessage":
        if (!checkControlLevel1($postData)) break;
        if (isset($postData->userId) && isset($postData->sessionId) && isset($postData->type) && isset($postData->content)) {
            $user = new User($postData->userId);
            if (!$user) break;
            if (!$user->sendMessage($postData->sessionId, $postData->type, $postData->content)) break;
            OKResponse();
        }
        break;
    case "sessionSetWaiter":
        if (!checkControlLevel2($postData)) break;
        if (isset($postData->sessionId) && isset($postData->waiterId)) {
            $session = new Session($postData->sessionId);
            if (!$session) break;
            if (!$session->setWaiter($postData->waiterId)) break;
            OKResponse();
        }
        break;
    case "getSessionNewestMessages":
        if (!checkControlLevel2($postData) && !checkControlLevel1($postData)) break;
        if (isset($postData->sessionId)) {
            if (!isset($postData->lastTime)) $postData->lastTime = 0;
            $session = new Session($postData->sessionId);
            if (!$session) break;
            $result = $session->getNewestMessages($postData->lastTime);
            echo json_encode($result);
            exit(0);
        }
        break;
    case "getSessionLastMessages":
        if (!checkControlLevel2($postData) && !checkControlLevel1($postData)) break;
        if (isset($postData->sessionId)) {
            if (!isset($postData->num)) $postData->num = 30;
            $session = new Session($postData->sessionId);
            if (!$session) break;
            $result = $session->getLastMessages($postData->num);
            echo json_encode($result);
            exit(0);
        }
        break;
    case "getSessionAddition":
        if (!checkControlLevel2($postData)) break;
        if (isset($postData->sessionId)) {
            $session = new Session($postData->sessionId);
            if (!$session) break;
            $result = $session->getAddition();
            echo json_encode($result);
            exit(0);
        }
        break;
    case "getUserLastSession":
        if (!checkControlLevel1($postData)) break;
        if (isset($postData->userId)) {
            $sm = new SessionManager();
            $result = $sm->getUserLastSession($postData->userId);
            if (!$result) break;
            echo json_encode(['_id' => $result->getId()]);
            exit(0);
        }
        break;
    case "getReadySessions":
        if (!checkControlLevel2($postData)) break;
        $sm = new SessionManager();
        $result = $sm->getReadySessions();
        echo json_encode($result);
        exit(0);
        break;
}

header("Content-Type: application/json");
echo '{"code": -1, "msg": "fail"}';
