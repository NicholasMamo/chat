<?php
	if (isset($nextTimestamp) && $nextTimestamp > 0 && isset($url)) { // if the required data is available
		$timestamp = $nextTimestamp - ADVANCE * 60; // retrieve the timestamp of the next game and remove the advance to get the time when the chatroom should start
	} else {
		$timestamp = 0; // otherwise, reset the timestamp to a default value
		$url = "http://lyon-forums.com"; // reset the URL to a default value
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="style/stylesheet.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script src="scripts/js/closed.js"></script>
		<script src="scripts/js/GA.js"></script>
		<title>OLF Chatroom - Closed</title>
	</head>
	<body onload = "init(<?php echo $timestamp; ?>);">
		<div id="container">
			<div id="splash">
				<img alt="Pratos - Lyon Forums" class="logo" src="http://chat.lyon-forums.com/style/assets/pratos_logo.svg">
				<h2>It seems like the chatroom is currently closed!</h2>
				<h3>Come back in <span name = "time"></span> to join the chatroom
					<br/>
					In the meantime, you can join the discussion on <a alt = "game topic" href = "<?php echo $url; ?>">lyon-forums.com</a>
				</h3>
			</div>
		</div>
	</body>
</html>