User
======================
<?php
include_once("php_includes/check_login_status.php");
// If user is already logged in, header that weenis away
if($user_ok == false){
	header("location: index.php?u=".$_SESSION["username"]);
    exit();
}
?><?php
// AJAX CALLS THIS LOGIN CODE TO EXECUTE
if(isset($_POST["e"])){
	// CONNECT TO THE DATABASE
	include_once("php_includes/db_conx.php");
	// GATHER THE POSTED DATA INTO LOCAL VARIABLES AND SANITIZE
	$e = mysqli_real_escape_string($db_conx, $_POST['e']);
	$p = md5($_POST['p']);
	// GET USER IP ADDRESS
    $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
	// FORM DATA ERROR HANDLING
	if($e == "" || $p == ""){
		echo "login_failed";
        exit();
	} else {
	// END FORM DATA ERROR HANDLING
		$sql = "SELECT id, username, password FROM users WHERE username='$e' AND activated='1' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
        $row = mysqli_fetch_row($query);
		$db_id = $row[0];
		$db_username = $row[1];
        $db_pass_str = $row[2];
		if($p != $db_pass_str){
			echo "login_failed";
            exit();
		} else {
			// CREATE THEIR SESSIONS AND COOKIES
			$_SESSION['userid'] = $db_id;
			$_SESSION['username'] = $db_username;
			$_SESSION['password'] = $db_pass_str;
			setcookie("id", $db_id, strtotime( '+30 days' ), "/", "", "", TRUE);
			setcookie("user", $db_username, strtotime( '+30 days' ), "/", "", "", TRUE);
    		setcookie("pass", $db_pass_str, strtotime( '+30 days' ), "/", "", "", TRUE); 
			// UPDATE THEIR "IP" AND "LASTLOGIN" FIELDS
			$sql = "UPDATE users SET ip='$ip', lastlogin=now() WHERE username='$db_username' LIMIT 1";
            $query = mysqli_query($db_conx, $sql);
			echo $db_username;
		    exit();
		}
	}
	exit();
//		_("loginbtn").style.display = "none";
//		_("status").innerHTML = 'please wait ...';
}
?>
<html>
<head>
<title>TheCentraNet</title>
<link rel="stylesheet" type="text/css" href="style.css">
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
<script>
function emptyElement(x){
	_(x).innerHTML = "";
}
function login(){
	var e = _("username").value;
	var p = _("password").value;
	if(e == "" || p == ""){
		_("status").innerHTML = "Forget Something?";
	} else {
		var ajax = ajaxObj("POST", "index.php");
        ajax.onreadystatechange = function() {
	        if(ajaxReturn(ajax) == true) {
	            if(ajax.responseText == "login_failed"){
					_("status").innerHTML = "That's Not Right";
                    _("loginbtn").style.display = "inline";
				} else {
					window.location = ""+ajax.responseText;
				}
	        }
        }
        ajax.send("e="+e+"&p="+p);
	}
}
</script>
</head>
<body>
<div id="head">
    <center>
    <table width="95%" height="80px"><tr><td width="25%"><img src="logo.png"></td><td wdith="25%" align="right"><?php 
if($user_ok == true){ 
$sql = "SELECT * FROM users WHERE username='$log_username' LIMIT 1";
$user_query = mysqli_query($db_conx, $sql);
while ($row = mysqli_fetch_array($user_query, MYSQLI_ASSOC)) {
	$first_name = $row["first_name"];
}
echo 'Hey '.$first_name.' <a href="http://keegantek.com/".$log_username." class="link">My Profile</a> <a href="logout.php" class="button">Sign Out</a>';
} else {
echo '<a href="create.php" class="link">Create Your Own Net</a>&nbsp <a href="signin.php" class="button">Sign In</a>';
} 
?></td></tds></tr></table></center>
</div>
<div id="main">
<center>
    <table width="85%">
<tr><td>
</td></tr>
</table>
</div>
</div>
</body>
</html>
