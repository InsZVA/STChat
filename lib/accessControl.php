<?php
/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/6/12
 * Time: 18:35
 */
session_start();

function checkControlLevel1($postData) { //Level1 用户级别
    return true;
    return intval($_SESSION['ACLevel']) == 1 || intval($_SESSION['ACLevel']) == 3;
}

function checkControlLevel2($postData) { //Level2 客服级别
    return true;
    return intval($_SESSION['ACLevel']) == 2 || intval($_SESSION['ACLevel']) == 3;
}

function checkControlLevel3($postData) { //Level3 管理员级别
    return true;
    return intval($_SESSION['ACLevel']) == 3;
}