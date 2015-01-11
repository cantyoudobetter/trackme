<?php 
include_once ("./include/session.php");
if (!$session->logged_in) {header( 'Location: login.php' );}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="content-language" content="en" />
	<meta name="robots" content="noindex,nofollow" />
	<link rel="stylesheet" media="screen,projection" type="text/css" href="css/reset.css" /> <!-- RESET -->
	<link rel="stylesheet" media="screen,projection" type="text/css" href="css/main.css" /> <!-- MAIN STYLE SHEET -->
	<link rel="stylesheet" media="screen,projection" type="text/css" href="css/2col.css" title="2col" /> <!-- DEFAULT: 2 COLUMNS -->
	<link rel="alternate stylesheet" media="screen,projection" type="text/css" href="css/1col.css" title="1col" /> <!-- ALTERNATE: 1 COLUMN -->
	<!--[if lte IE 6]><link rel="stylesheet" media="screen,projection" type="text/css" href="css/main-ie6.css" /><![endif]--> <!-- MSIE6 -->
	<link rel="stylesheet" media="screen,projection" type="text/css" href="css/style.css" /> <!-- GRAPHIC THEME -->
	<link rel="stylesheet" media="screen,projection" type="text/css" href="css/mystyle.css" /> <!-- WRITE YOUR CSS CODE HERE -->
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/jquery.tablesorter.js"></script>
	<script type="text/javascript" src="js/jquery.switcher.js"></script>
	<script type="text/javascript" src="js/toggle.js"></script>
	<script type="text/javascript" src="js/ui.core.js"></script>
	<script type="text/javascript" src="js/ui.tabs.js"></script>
	<script type="text/javascript">
	$(document).ready(function(){
		$(".tabs > ul").tabs();
		$.tablesorter.defaults.sortList = [[1,0]];
		$("table").tablesorter({
			headers: {
			}
		});
		$('table td img.changestatus').click(function(){
    		$(this).parent().parent().remove();
			$.get('changestatus.php', {id: $(this).attr('id'), newstatus: $(this).attr('newstatus')}, function(){});

		});
	});

	//function changeStatus(toggle, theID, newStatus) {
	//	$(this).parent().parent().remove();
	//}
	
	function flipCheck(toggle,theID) {
   		$.ajax({
    	   type: "GET",
    	   url: "flipCheck.php",
    	   data: "id="+theID+"&check="+toggle.id,
    	   success: function(msg){
				toggle.id = msg;
				if (msg == "1")
				{
					toggle.src = "design/ico-done.gif";
				} else {
					toggle.src = "design/uncheck.png";
				}
   		   }
    	 });
	}

	function flipLock(toggle,theID) {
   		$.ajax({
    	   type: "GET",
    	   url: "flipLock.php",
    	   data: "id="+theID+"&lock="+toggle.id,
    	   success: function(msg){
				toggle.id = msg;
				if (msg == "1")
				{
					toggle.src = "design/lock-icon.png";
				} else {
					toggle.src = "design/unlock-icon.png";
				}
   		   }
    	 });
	}

	</script>
	<title>Physical Therapy International</title>
</head>
<body>

<div id="main">

	<!-- Tray -->
	<div id="tray" class="box">

		<p class="f-left box">
			System: <strong>PTI Forms Admininistration</strong>
		</p>

		<p class="f-right">User: <strong><a href="#"><?php echo "$session->username"?></a></strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong><a href="process.php">Log out</a></strong></p>

	</div> <!--  /tray -->

	<hr class="noscreen" />
	</div> <!-- /header -->

	<hr class="noscreen" />