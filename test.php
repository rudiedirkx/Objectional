<pre><?php

$fStart = microtime(1);

require_once('inc.cls.object.php');

$db = new Object('./database');
//print_r($db);

//print_r($db->getObjects());

$test2 = $db->object('test2');
echo '&test2: ';
print_r($test2);

echo 'children: ';
print_r($test2->getObjectIDs());

$newname = 'oele'.rand(1, 99);
echo 'create "'.$newname.'": ';
var_dump($test2->create($newname));

$oele = $test2->object($newname);
echo '&oele: ';
print_r($oele);

$oelesparent = $oele->parent(1);
echo '&oele\'s parent: ';
print_r($oelesparent);

echo '&oele\'s parent\'s children: ';
print_r($oelesparent->getObjects());


echo "\n".number_format(microtime(1)-$fStart, 4)." s\n";