<pre><?php

$fStart = microtime(1);

require_once('inc.cls.object.php');

$db = new Object('./database');

if ( isset($_GET['id']) ) {
	$object = $db->getObjectByPath($_GET['id']);
	var_dump($object);
	if ( isset($_GET['addtype'], $_GET['addkey'], $_GET['addvalue']) ) {
		$type = in_array($_GET['addtype'], array('int', 'float', 'bool')) ? $_GET['addtype'] : 'string';
		$conv = create_function('$v', 'return ('.$type.')$v;');
		$key = $_GET['addkey'];
		$value = $conv($_GET['addvalue']);
		$object->set($key, $value);
	}
	else {
		echo '<form method=get action="">';
		echo '<input type=hidden name=id value="'.htmlspecialchars($_GET['id']).'">';
		echo '<fieldset><legend>Add/change property</legend>';
		echo '<p>Type:<br><input name=addtype></p>';
		echo '<p>Name:<br><input name=addkey></p>';
		echo '<p>Value:<br><input name=addvalue></p>';
		echo '<p><input type=submit></p>';
		echo '</fieldset>';
		echo '</form>';
	}
	var_dump($object->asArray());
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

