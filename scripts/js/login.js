function validateUsername() {
	var name = $("input[name = username]").val();
	$("#warning").text("");

	$.post("http://chat.lyon-forums.com/scripts/ajax/login.php",
		{
			username: name
		},
		function(data, status){
			//alert("Data: " + data + "\nStatus: " + status);
			if (data != "VALID") {
				$("#warning").text(data);
			} else {
				var form = $("<form action = 'http://chat.lyon-forums.com/' method = 'post'>" +
				"<input type = 'hidden' name = 'username' value = '" + name + "'>" + name + "</input></form>");
				$('body').append(form);
				$(form).submit();
			}
		});

}