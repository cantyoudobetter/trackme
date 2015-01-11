<html>
<body>
<table border="1">
<strong><FONT SIZE=6>THE POLITO TRACKER</FONT></strong>

<br><FONT SIZE=5><a href="javascript:location.reload(true);">Refresh Page</a></FONT> <br><br>

<?php 


   mysql_connect("mbordelon.db.4294663.hostedresource.com", "mbordelon", "Tamu1993") or die(mysql_error());
   mysql_select_db("mbordelon") or die(mysql_error());
   $q2 = "SELECT location, timestamp FROM tracker order by timestamp DESC LIMIT 0,30";


   $result = mysql_query($q2);
   $num_rows = mysql_numrows($result);

   if($num_rows == 0){
      echo "No Records Found - Come back later";
      return;
   } else {

   /* Display table contents */
   $location = mysql_result($result,0,"location");
   echo "<BIG><a href='http://maps.google.com/maps?q=$location'>CURRENT POSITION</a></BIG><br><br>";
   //echo "<iframe width='425' height='350' frameborder='0' scrolling='no' marginheight='0' marginwidth='0' src='http://maps.google.com/maps?q=$location&amp;ie=UTF8&amp;t=m&amp;z=14&amp;vpsrc=0&amp;ll=$location&amp;output=embed'></iframe><br /><small><a href='http://maps.google.com/maps?q=$location&amp;ie=UTF8&amp;t=m&amp;z=14&amp;vpsrc=0&amp;ll=$location&amp;source=embed' style='color:#0000FF;text-align:left'>View Larger Map</a></small><br /><br><br>";
   $all = "";
   for($i=0; $i<$num_rows; $i++){
      $location = mysql_result($result,$i,"location");
      $ts = mysql_result($result,$i,"timestamp");
      $ts = $ts+60*60*2;
      $dt = date("m-d-Y h:i A", $ts);
      //$dt->add(new DateInterval('P2H'));
      echo "<tr><td><a href='http://maps.google.com/maps?q=$location'>$dt</a></td></tr>";
      if ($i==0) {
        $all = "q=$location";
      } else {
        $all = $all."&q=$location";
      }
   }
    //echo "<tr><td> </tr></td><tr><td><a href='http://maps.google.com/maps?$all'>PLOT ALL</a></td></tr>";

   }

?>
</table>
</body>
</html>