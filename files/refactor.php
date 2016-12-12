<?php

include_once(realpath(dirname(__FILE__)) . "/FileManager.php");

$messages = FileManager::getAllMessages();
$new = array();

for ($i = 0; $i < count($messages); $i++) {
	$message = $messages[$i];
	$message["id"] = $i;
}

echo json_encode($new);

?>