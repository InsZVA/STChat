<?php

/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/6/12
 * Time: 12:56
 */

const SESSION_STATE_READY = -1;
const SESSION_STATE_OPEN = 0;
const SESSION_STATE_CLOSE = 1;

const SESSION_SENDER_USER = 0;
const SESSION_SENDER_WAITER = 1;

class Config
{
    static public $mongoAddr = "mongodb://localhost:27017";
    static public $database = 'STChat';
}