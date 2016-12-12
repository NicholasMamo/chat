<?php

include_once(realpath(dirname(__FILE__)) . "/database/CookieManager.php");

?>

<!DOCTYPE html>
<html>
	<head>
		<link rel = "stylesheet" href = "style/stylesheet.css?r=1">
		<meta name = "viewport" content = "width=device-width, initial-scale=1.0">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script src = "scripts/js/chat.js?r=2"></script>
		<script src = "scripts/js/drawer.js"></script>
		<script src="scripts/js/GA.js"></script>
		<title>OLF Chatroom</title>
	</head>
	<body class = "light">
		<div id = "container">
			<div id = "sidebar">
				<ul>
					<li><a alt = "Lyon Forums" href = "http://lyon-forums.com/" target = "_blank"><img alt = "users" src = "/style/assets/icons_forums.svg"></img></a></li>
					<li onclick = "toggle('users');"><img alt = "users" src = "/style/assets/icons_users.svg"></img><span id = "sidebar_user_count">1</span></li>
					<li onclick = "toggle('streams');"><img alt = "streams" src = "/style/assets/icons_streams.svg"></img></li>
				</ul>
			</div>
			<div id = "drawer" class = "drawer_closed">
				<div id = "drawer_content">
					<h3></h3>
					<div id = "drawer_content_list">
					</div>
				</div>
			</div>
			<div id = "chat_container"  class = "chat_wide">
				<div id = "chat">
					<div id = "messages">
						<div class="message">
							<div class="message_info">
								<div class="message_info_avatar">
									<div class="message_info_avatar_inner">
										<img src="http://lyon-forums.com/download/file.php?avatar=997_1469791123.png">
									</div>
								</div>
								<div class="message_info_username">PRATOS</div>
								<div class="message_info_break"></div>
							</div>
							<div class="message_block">
								<div class="message_block_line">
									<p>Hi there, I'm Pratos, your host!</p>
								</div>
								<div class="message_block_line">
									<p>Get started by choosing a stream from the sidebar, or say hi to the rest of the members!</p>
								</div>
							</div>
						</div>
					</div>
				</div><div id = "input">
					<form onsubmit = "addMessage(); return false;">
						<input name = "clean" type = "hidden" value = "<?php echo strtolower(CookieManager::getIdentifier()); ?>">
						<input autocomplete = "off" name = "new" onfocus = "reposition(); delayedScroll();" oninput = "scrollChat()" placeholder = "NEW MESSAGE" type = "text"></input>
					</form>
					<img alt = "submit" onclick = "addMessage();" src = "/style/assets/icons_arrow.svg" class = "submit"></img>
				</div>
			</div>
		</div>
	</body>
</html>