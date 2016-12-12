var timestamp = 0;

/**
 * Initialize the routine for the closed chatroom
 * @param time The timestamp of the next game
 */
function init(time) {
	timestamp = time; // copy the timestamp
	updateTime(); // update the time
	setInterval(updateTime, 60000); // every minute, update the remaining time
}

/**
 * Update the time remaining until the chatroom opens
 */
function updateTime() {
	var quantity = 0; // the quantity of units until the chatroom opens
	var units = ""; // the units that the quantity is measured with
	
	var now = (new Date().getTime()) / 1000; // get the current timestamp
	var difference = (timestamp - now) / 60; // get the difference in minutes until the next game
	
	if (difference <= 0) { // if the chatroom has opened
		window.location = "http://chat.lyon-forums.com"; // refresh the page so that the user is redirected into the chatroom
	} else if (difference <= 1) { // if a minute remains
		quantity = Math.ceil(difference); // copy the difference in minutes
		units = "minute"; // the units is a minute
	} else if (difference <= 59) { // if less than an hour remains
		quantity = Math.ceil(difference); // copy the difference in minutes
		units = "minutes"; // a number of minutes remain
	} else { // an hour or more remains until the chatroom is open
		difference = Math.round(difference / 60); // deal with hours
		quantity = difference; // copy the difference in hours
		if (difference <= 1) { // if an hour remains
			units = "hour"; // the unit is an hour
		} else {
			units = "hours"; // the unit is in hours
		}
	}
	
	$("[name=time]").text(quantity + " " + units); // update the remaining time
}