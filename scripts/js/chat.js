const DEFAULTTIMEOUT = 100; // the default time (in milliseconds) to wait before refreshing checks for messages
const MAXDIFFERENCE = 5; // the default time (in minutes) that should split two blocks of messages by the same user
const SCROLLTHRESHOLD = 300; // the minimum number of pixels that will be considered as being at the bottom of the chat

updateTimestamp(); // upon loading, update the user's timestamp

function Comet() {
	
	this.timestamp = 0; // used to retrieve new messages only
	this.id = -1; // used to retrieve new messages only - the last ID read
	this.timeout = DEFAULTTIMEOUT; // time to wait before the next AJAX call
	this.updateTitle = false; // a boolean indicating whether the page's title should be updated (true) or not (false)
	
	this.initialize = function() {}; // constructor
	
	this.connect = function() { // main function to retrieve new messages
		var self = this; // used to be able to call the message processor
		$.ajax(
			{
				error: function(xhr, result, status){ // if the call ends in an error
					self.timeout = DEFAULTTIMEOUT * 10; // in case of an error, the chat should slow down, allowing for any changes (for example, for connection errors to be fixed)
					setTimeout(function(){ // call the connect function again
							comet.connect()
						},
						self.timeout); // wait more time, however
					//console.log("Error: " + status); // display the error message
				},
				data: {
					timestamp: self.timestamp, // the data to pass in the request
					id: self.id // the data to pass in the request
				},
				success: function(result, status){ // if the call ends successfully
					self.timeout = DEFAULTTIMEOUT; // reset the waiting time
					setTimeout(function(){ // call the connect function again
							comet.connect()
						},
						self.timeout);
					var response = jQuery.parseJSON(result); // get the response from the JSON string
					self.timestamp = response.timestamp; // get the result's timestamp
					self.id = response.id; // get the result's ID
					processMessages(response.messages); // process the messages returned
					if (self.updateTitle && response.messages.length > 0) { // if the title should be updated and there are indeed new messages
						document.title = "New messages"; // update it
					}
					//console.log("Data: " + result + "\nStatus: " + status);
				},
				type: "get", // use a get request
				url: "http://chat.lyon-forums.com/scripts/ajax/getMessages.php" // the url to use in conjunction with the AJAX request
			});
	};
	
	this.print = function() {
			console.log(this.timeout);
			console.log(this.id);
		};
	
}

/**
 * The function that receives messages, and processes them to dispaly them in the chatroom
 * @param messages The messages to process
 */
function processMessages(messages) {
	var scroll = (getMaxScroll() - $("#chat").scrollTop()) < SCROLLTHRESHOLD; // the chat should only scroll if the user is already at its bottom, and not looking at old messages
	
	jQuery.each(messages, function(index, message) { // go through each message
		var timestamp = $(".message_info_timestamp").last().attr("value"); // get the last timestamp
		var date; // the date of the last message
		if (timestamp) {
			var hours = timestamp.substring(0, timestamp.indexOf(":")); // get the hours
			var minutes = timestamp.substring(timestamp.indexOf(":") + 1); // get the minutes
			date = new Date(0, 0, 0, hours, minutes).valueOf()/(1000 * 60); // get the date as a timestamp in minutes
		} else {
			date = new Date(); // initialize an empty date
		}
		
		var message_date = new Date(message.timestamp * 1000); // get the date (multiplication due to the fact that UNIX timestamps should be in milliseconds)
		message_date = new Date(0, 0, 0, message_date.getHours(), message_date.getMinutes()).valueOf()/(1000 * 60); // get the date as a timestamp in minutes
			
		if ((message.clean == getLastUsername() || (message.clean == $("[name=clean]").val() && getLastUsername() == "you")) && Math.abs(message_date - date) < MAXDIFFERENCE) { // if the last message was posted by the same person who posted the current message
			var message_block_line = $("<div class = \"message_block_line\">"); // create a new message line
			message_block_line.html("<p>" + message.message + "</p>"); // update its content
			$(".message_block").last().append(message_block_line); // append it to the last message block
			
			var date = new Date(message.timestamp * 1000); // get the date (multiplication due to the fact that UNIX timestamps should be in milliseconds)
			$(".message_info_timestamp").last().attr("value", ("0" + date.getHours()).slice(-2) + ":" + ("0" + date.getMinutes()).slice(-2));
		} else { // if the last message was posted by someone else
			var message_container = $("<div class = \"message\"></div>"); // create the message container
			
			var message_info = $("<div class = \"message_info\"></div>"); // create the message information container
			var message_info_avatar = $("<div class = \"message_info_avatar\"></div>"); // create the avatar container
			var message_info_avatar_inner = $("<div class = \"message_info_avatar_inner\"></div>"); // create the inner avatar container
			var message_info_username = $("<div class = \"message_info_username\"></div>"); // create the username container
			var message_info_break = $("<div class = \"message_info_break\"></div>"); // create the break
			var message_info_timestamp = $("<div class = \"message_info_timestamp\"></div>"); // create the timestamp container
			message_info.append(message_info_avatar, message_info_username, message_info_break, message_info_timestamp); // append the information to the information container
			
			var message_block = $("<div class = \"message_block\">"); // create the message block
			var message_block_line = $("<div class = \"message_block_line\">"); // create a new message line
			message_block_line.html("<p>" + message.message + "</p>"); // update its content
			message_block.append(message_block_line); // append it to the message block
			
			message_container.append(message_info, message_block); // append to the message the information and block
			$("#messages").append(message_container); // append the message to the rest of the messages
			
			if (message.clean == $("[name=clean]").val()) { // if the message was posted by the user
				message_info_username.text("YOU"); // change the username
				message_container.addClass("own"); // add to it a class that shows it was self-posted
			} else {
				message_info_username.text(message.clean.toUpperCase()); // otherwise update the username as usual
			}
			
			if (message.avatar) {
				message_info_avatar.append(message_info_avatar_inner);
				message_info_avatar_inner.html("<img src = \"" + message.avatar + "\">"); // change the username
			}
			
			var date = new Date(message.timestamp * 1000); // get the date (multiplication due to the fact that UNIX timestamps should be in milliseconds)
			message_info_timestamp.text(("0" + date.getHours()).slice(-2) + ":" + ("0" + date.getMinutes()).slice(-2)); // update the timestamp and format it as hh:ii
			$(".message_info_timestamp").last().attr("value", ("0" + date.getHours()).slice(-2) + ":" + ("0" + date.getMinutes()).slice(-2));
			
		}
		
	});
	
	if (scroll && $("#chat").scrollTop() != getMaxScroll()) { // if the chat should be scrolled to the bottom and it can be scrolled
		scrollChat(); // scroll the chat
	}
}

/**
 * Add a message by the user to the list of messages
 */
function addMessage(message) {
	var content = "";
	if (typeof message == 'undefined') { // if no content was passed on
		content = $("[name=new]").val(); // fetch the message from the input field
		$("[name=new]").val(""); // reset the input field
	} else {
		content = message; // the message content is the parameter
	}
	
	$.ajax(
		{
			error: function(xhr, result, status){ // if the call ends in an error
				setTimeout(function(){ // call the connect function again
						addMessage(content)
					},
					1000); // wait one second and try re-adding the message again
				//console.log("Error: " + status); // display the error message
			},
			data: {
				message: content // only pass the message - the other variables will be determined on the server-side
			},
			success: function(result, status){ // if the call ends successfully
				if (result == "1") { // if the result could be added
					ga('create', 'UA-XXXX-Y', 'auto'); // connect to Google Analytics
					ga('set', 'metric1', 1 ); // increment the number of messages
					ga('send', 'event', 'Message', $("[name=clean]").val()); // set the dimension
				}
				//console.log("Data: " + result + "\nStatus: " + status);
			},
			type: "post", // use a post request
			url: "http://chat.lyon-forums.com/scripts/ajax/addMessage.php" // the url to use in conjunction with the AJAX request
		});
}

/**
 * Get the maximum scrolling distance in the chat
 * @return The maximum scrolling distance in the chat
 */
function getMaxScroll() {
	var paddingTop = $("#chat").css("padding-top"); // get the top padding of the chat container
	var paddingBottom = $("#chat").css("padding-bottom"); // get the bottom padding of the chat container
	var padding = parseInt(paddingTop.substr(0, paddingTop.length - 2)) + parseInt(paddingBottom.substr(0, paddingBottom.length - 2)); // get the total padding of the chat container
	return $("#messages").outerHeight() - $("#chat").innerHeight() + padding;
}

/**
 * Solves a bug wherein the container scrolls down when the user navigates with the tab key to the input field
 */
function reposition() {
	$("#container").scrollTop(0);
}

/**
 * Solves a bug wherein the chat is hidden by the keyboard on mobile devices once it loads
 */
function delayedScroll() {
	setTimeout(scrollChat, 1000); // wait some time, then scroll
}

/**
 * Scroll the chat container to show all the recent messages
 */
function scrollChat() {
	$("#chat").stop(true, false);
	$("#chat").animate({scrollTop: getMaxScroll()}, 100); // scroll down slowly
}

/**
 * Get the username of the last person to post to the chatroom
 * @return The username of the last person to post to the chatroom
 */
function getLastUsername() {
	return $(".message").last().find($(".message_info_username")).text().trim().toLowerCase(); // get the last post username
}

/**
 * Update the user's timestamp
 */
function updateTimestamp() {
	$.ajax(
		{
			type: "post", // use a post request
			url: "http://chat.lyon-forums.com/scripts/ajax/updateTimestamp.php" // the url to use in conjunction with the AJAX request
		});
}

var comet = new Comet(); // create the comet object
comet.connect(); // start retrieving messages

$(window).focus(function() {  // when the window comes into focus
	document.title = "OLF Chatroom"; // reset the title
	comet.timeout = DEFAULTTIMEOUT; // reset the timeout
	comet.updateTitle = false; // mark the comet instance so that it no longer updates the title
	});

$(window).blur(function() {
	comet.timeout = DEFAULTTIMEOUT * 10; // slow down the timeout in-between messages
	comet.updateTitle = true; // mark the comet instance so that it  updates the title
});