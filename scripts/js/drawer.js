const RECENCY = 5; // the time threshold for user activity (in minutes)

var currentArea = ""; // the default area shown is the user area

$(document).ready(function() { // once the document is ready
	if ($(window).width() < 920) { // if this is a mobile device
		closeDrawer(); // close the drawer
	} else {
		toggle('users'); // otherwise, show the users tab
	}
	scrollChat(); // scroll the chatroom
	getActive(); // update the number of active users in the chatroom
});

/**
 * Get the number of active users and display it in the sidebar
 */
function getActive() {
	$.ajax(
		{
			data: {
				recency: RECENCY
			},
			success: function(result, status) {
				$("#sidebar_user_count").text(result); // update the count with the number of active users
			},
			type: "get", // use a post request
			url: "http://chat.lyon-forums.com/scripts/ajax/getActive.php", // the url to use in conjunction with the AJAX request
		}); // get the active user count
}

/**
 * Close the drawer
 */
function closeDrawer() {
	currentArea = ""; // no area is thus currently on display
	$("#drawer").removeClass("drawer_open"); // remove the open class from the drawer
	$("#drawer").removeClass("drawer_closed"); // also ensure that there are no duplicate classes
	$("#drawer").addClass("drawer_closed"); // close the drawer
	$("#chat_container").removeClass("chat_narrow");
	$("#chat_container").addClass("chat_wide");
}

/**
 * Draw the content in the drawer
 * @param content The content that will be shown in the drawer
 */
function processContent(content) {
	var newContent = ""; // the new content in the drawer
	
	if (currentArea == "users") { // if the current area is the user area
		newContent = "<ul>\n";
		jQuery.each(content, function(index, item) { // go through each item
			newContent += "<li>" + item.username + "</li>\n";
		});
		newContent += "<ul>";
	} else if (currentArea == "streams") {
		newContent = "<ul>\n";
		jQuery.each(content, function(index, item) { // go through each item
			//console.log(item.stream);
			newContent += "<li><a href = '" + item.stream + "' target = '_blank'>STREAM " + (index + 1) + "</a></li>\n";
		});
		newContent += "<ul>";
	}
	
	$("#drawer_content_list").html(newContent); // update the content
}

/**
 * The function that is responsible for dealing with the drawer
 * @param area The area that should be shown
 */
function toggle(area) {
	var scroll = (getMaxScroll() - $("#chat").scrollTop()) < SCROLLTHRESHOLD; // the chat should only scroll if the user is already at its bottom, and not looking at old messages
	if (currentArea == area) { // if the user wants to toggle the area on display, close the drawer
		$("#drawer").removeClass("drawer_open"); // remove the open class from the drawer
		$("#drawer").addClass("drawer_closed"); // close the drawer
		currentArea = ""; // no area is currently on display
	} else { // otherwise, a different ara is currently being displayed, or no area at all is on display
		$("#drawer").removeClass("drawer_open"); // remove the open class from the drawer
		$("#drawer").addClass("drawer_closed"); // close the drawer
		var ready = false; // a boolean indicating whether the new content has been fetched (true) or not (false)
		var timeout = (currentArea == ""); // a boolean indicating whether the drawer has been closed (true) or not (false) - waiting should only happen if the drawer is opened
		var response; // the AJAX response
		
		$.ajax(
			{
				data: {
					type: area,
					recency: RECENCY
				},
				success: function(result, status){ // if the call ends successfully
					response = jQuery.parseJSON(result); // get the response from the JSON string
					if (timeout) { // if the drawer has been closed
						$("#drawer").find("h3").text(response.title); // update the title
						processContent(response.content); // process the content received
						$("#drawer").removeClass("drawer_closed"); // remove the closed class from the drawer
						$("#drawer").addClass("drawer_open"); // open the drawer
					} else {
						ready = true; // otherwise mark the flag so that once the drawer is closed, it may be opened
					}
					//console.log("Data: " + result + "\nStatus: " + status);
				},
				type: "get", // use a get request
				url: "http://chat.lyon-forums.com/scripts/ajax/show.php" // the url to use in conjunction with the AJAX request
			});
		
		setTimeout(function() {
			if (ready) { // if the content has been fetched
				$("#drawer").find("h3").text(response.title); // update the title
				processContent(response.content); // process the content received
				$("#drawer").removeClass("drawer_closed"); // remove the closed class from the drawer
				$("#drawer").addClass("drawer_open"); // open the drawer
			} else {
				timeout = true; // otherwise mark the flag so that once the content is fetched, the drawer may be opened
			}
		}, 500); // wait half a second, then check whether the drawer should be opened
		
		currentArea = area; // update the area currently on display
	}
	
	if (currentArea == "") {
		$("#chat_container").removeClass("chat_narrow");
		$("#chat_container").addClass("chat_wide");
	} else {		
		$("#chat_container").removeClass("chat_wide");
		$("#chat_container").addClass("chat_narrow");
	}
	
	if (scroll) {
		setTimeout(scrollChat, 500);
	}
}

/**
 * The function that is responsible for updating the drawer
 */
function updateDrawer() {
	$.ajax(
			{
				data: {
					type: currentArea,
					recency: RECENCY
				},
				success: function(result, status){ // if the call ends successfully
					response = jQuery.parseJSON(result); // get the response from the JSON string
					processContent(response.content); // process the content received
					//console.log("Data: " + result + "\nStatus: " + status);
				},
				type: "get", // use a get request
				url: "http://chat.lyon-forums.com/scripts/ajax/update.php" // the url to use in conjunction with the AJAX request
			});
}



setInterval(updateTimestamp, 60000); // every minute update the user's timestamp
	
setInterval(getActive, 10000); // every ten seconds get the number of active users
	
setInterval(updateDrawer, 60000); // every minute update the drawer