<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="style/stylesheet.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script src="scripts/js/login.js"></script>
		<script src="scripts/js/GA.js"></script>
		<title>OLF Chatroom - Login</title>
	</head>
	<body>
		<div id="container">
			<div id="splash">
				<img alt="Pratos - Lyon Forums" class="logo" src="http://chat.lyon-forums.com/style/assets/pratos_logo.svg">
				<h2>Oops! It seems like you're not logged in!</h2>
				<h3>Log in at <a href="http://lyon-forums.com/">lyon-forums.com</a> or <br><hr>
				choose a username to get started</h3>
				<form onsubmit="validateUsername(); return false;">
					<label></label><input name="username" type="text"><input type="submit" value="JOIN">
  					<br>
					<div id="warning"></div>
				</form>
			</div>
		</div>
	</body>
</html>