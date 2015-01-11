<?include("./include/session.php");?>


<title>PTI</title>

<head>
<link rel="stylesheet" type="text/css" href="jqueryslidemenu.css" />

<!--[if lte IE 7]>
<style type="text/css">
html .jqueryslidemenu{height: 1%;} /*Holly Hack for IE7 and below*/
</style>
<![endif]-->

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js"></script>
<script type="text/javascript" src="jqueryslidemenu.js"></script>


</head>
<body>

<table>
<tr><td>
<?
/**
 * User has already logged in, so display relavent links, including
 * a link to the admin center if the user is an administrator.
 */
if($session->logged_in){
?>

   <div id="myslidemenu" class="jqueryslidemenu">
   <ul>
   	<li><a href="./main.php">home</a></li>
   	<li><a href="#">Admin</a>
   	  <ul>
   		<?
   		if($session->isAdmin()){
      	echo "<li><a href=\"./admin/admin.php\">admin</a></li>";
   		}
		echo "<li><a href=\"userinfo.php?user=$session->username\">my account</a></li>";
		?>
   		<li><a href="useredit.php">edit account</a></li>
   	  </ul>
   	</li>
   	<li><a href="process.php">logout</a></li>
   </ul>
   <br style="clear: left" />
   </div>

<?
   echo "Welcome <b>$session->username</b>, you are logged in.";
}
else{

	if($form->num_errors > 0){
	   echo "<font size=\"2\" color=\"#ff0000\">".$form->num_errors." error(s) found</font>";
	}
?>
	<h1>Log In</h1>
	<form action="process.php" method="POST">
	<table align="left" border="0" cellspacing="0" cellpadding="3">
	<tr><td>Username:</td><td><input type="text" name="user" maxlength="30" value="<? echo $form->value("user"); ?>"></td><td><? echo $form->error("user"); ?></td></tr>
	<tr><td>Password:</td><td><input type="password" name="pass" maxlength="30" value="<? echo $form->value("pass"); ?>"></td><td><? echo $form->error("pass"); ?></td></tr>
	<tr><td colspan="2" align="left"><input type="checkbox" name="remember" <? if($form->value("remember") != ""){ echo "checked"; } ?>>
	<font size="2">Remember me next time &nbsp;&nbsp;&nbsp;&nbsp;
	<input type="hidden" name="sublogin" value="1">
	<input type="submit" value="Login"></td></tr>
	<tr><td colspan="2" align="left"><br><font size="2">[<a href="forgotpass.php">Forgot Password?</a>]</font></td><td align="right"></td></tr>
	<tr><td colspan="2" align="left"><br>Not registered? <a href="register.php">Sign-Up!</a></td></tr>
	</table>
	</form>
<?
}
?>



</td></tr>
</table>