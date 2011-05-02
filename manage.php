<pre><?php

$fStart = microtime(1);

require_once('inc.cls.object.php');

$db = new Object('./database');

$_types = array('string', 'int', 'float', 'bool', 'array', 'object');

if ( isset($_GET['id']) ) {
	$object = $db->getObjectByPath($_GET['id']);
var_dump($object);
	if ( isset($_GET['addtype'], $_GET['addkey'], $_GET['addvalue']) ) {
		$type = in_array($_GET['addtype'], $_types) ? $_GET['addtype'] : 'string';
		$key = $_GET['addkey'];

		$value = $_GET['addvalue'];
#echo "pre json: ";
#var_dump($value);
		$conv = create_function('$v', 'return ('.$type.')$v;');
		if ( in_array($type, array('array', 'object')) ) {
			$value = json_decode($value);
		}
#echo "post json: ";
#var_dump($value);
		$value = $conv($value);
#echo "post conv: ";
#var_dump($value);

		$object->set($key, $value);
	}
	else {
		echo '<form method=get action="">';
		echo '<input type=hidden name=id value="'.htmlspecialchars($_GET['id']).'">';
		echo '<fieldset><legend>Add/update property</legend>';
		echo '<p>Type:<br><select name=addtype>'.implode(array_map(create_function('$t', 'return "<option value=\"".$t."\">".$t."</option>";'), $_types)).'</select></p>';
		echo '<p>Name:<br><input name=addkey></p>';
		echo '<p>Value:<br><textarea name=addvalue id=addvalue></textarea> (<a href="#" onclick="try{alert(\'You\\\'re good: \'+JSON.parse(document.getElementById(\'addvalue\').value));}catch(ex){alert(\'Invalid JSON: \'+ex.message);}return false;">verify json</a>)</p>';
		echo '<p><input type=submit></p>';
		echo '</fieldset>';
		echo '</form>';
	}
	var_dump($object->asArray());
	exit;
}

$children = getRecursiveChildren($db);
printRecursiveChildren($children);

function printRecursiveChildren( $list ) {
	echo '<ul>';
	foreach ( $list AS $id => $children ) {
		echo '<li><a href="?id='.urlencode($id).'">'.ltrim($id, '/')."</a>".( ($c=count($children)) ? " (".$c.")" : '' )."</li>";
		printRecursiveChildren($children);
	}
	echo '</ul>';
}
function getRecursiveChildren( $parent, $pre = '/' ) {
	$children = array();
	foreach ( $parent->getObjects() AS $id => $obj ) {
		$children[$pre.$id] = getRecursiveChildren($obj, $pre.$id.'/');
	}
	return $children;
}

