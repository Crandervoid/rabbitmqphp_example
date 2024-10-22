<html>
<script>

function HandleLoginResponse(response)
{
	var text = JSON.parse(response);
//	document.getElementById("textResponse").innerHTML = response+"<p>";	
	document.getElementById("textResponse").innerHTML = "response: "+text+"<p>";
}

function SendLoginRequest(username,password)
{
	var request = new XMLHttpRequest();
	request.open("POST","login.php",true);
	request.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	request.onreadystatechange= function ()
	{
		
		if ((this.readyState == 4)&&(this.status == 200))
		{
			HandleLoginResponse(this.responseText);
		}		
	}
	request.send("type=login&uname="+username+"&pword="+password);
}
</script>

<h1>login page</h1>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
<body>
	<form action="login.php" method="POST">
		<label>Username:</label>
		<input type="text" name="email" value=""><br>
		<label>Password:</label>
		<input type="password" name="password" value=""><br>
		<input type="submit" value="login">
	</form>
<div id="textResponse">
awaiting response
</div>
<script>
SendLoginRequest("kehoed","12345");
</script>
</body>
</html>
