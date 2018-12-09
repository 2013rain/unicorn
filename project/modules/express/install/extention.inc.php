<?php
defined('IN_PHPCMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');
$farid = $menu_db->insert(array('name'=>'express', 'parentid'=>10, 'm'=>'express', 'c'=>'index', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'1'), true);
$parentid = $menu_db->insert(array('name'=>'my_express', 'parentid'=>$farid, 'm'=>'express', 'c'=>'index', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'put_in_storage', 'parentid'=>$parentid, 'm'=>'express', 'c'=>'index', 'a'=>'in_storage', 'data'=>'', 'listorder'=>0, 'display'=>'1'));

$language = array('express'=>'快递操作', 'my_express'=>'我的货物', 'put_in_storage'=>'我要入库');
?>
