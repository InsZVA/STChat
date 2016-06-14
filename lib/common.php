<?php
/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/6/14
 * Time: 10:44
 */


function produceOId(&$array) {
    for ($i = 0;$i < count($array);$i++) {
        if (!isset($array[$i]->_id)) continue;
        $array[$i]->_id = $array[$i]->_id->__toString();
    }

}