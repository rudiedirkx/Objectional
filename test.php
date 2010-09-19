<pre><?php

require_once('inc.cls.object.php');

$db = new Object('./database');
//print_r($db);

//print_r($db->getObjects());

$test2 = $db->get('test2');
print_r($test2);

print_r($test2->getObjectIDs());

var_dump($test2->create('oele'));


