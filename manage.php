<pre><?php

$fStart = microtime(1);

require_once('inc.cls.object.php');

$db = new Object('./database');

if ( isset($_GET['id']) ) {
	$object = $db->getObjectByPath($_GET['id']);
	var_dump($object);
	exit;
}

$children = getRecursiveChildren($db);
print_r($children);


function getRecursiveChildren( $parent, $pre = '/' ) {
	$children = array();
	foreach ( $parent->getObjects() AS $id => $obj ) {
echo '<a href="?id='.urlencode($pre.$id).'">'.$pre.$id."</a>\n";
		$children[$pre.$id] = getRecursiveChildren($obj, $pre.$id.'/');
	}
	return $children;
}

