<?
/**
 * Database.php
 *
 * The Database class is meant to simplify the task of accessing
 * information from the website's database.
 *
 * Written by: Jpmaster77 a.k.a. The Grandmaster of C++ (GMC)
 * Last Updated: August 17, 2004
 */
include_once ("constants.php");
include_once ("classes.php");

class MySQLDB
{
   var $connection;         //The MySQL database connection
   var $num_active_users;   //Number of active users viewing site
   var $num_active_guests;  //Number of active guests viewing site
   var $num_members;        //Number of signed-up users
   /* Note: call getNumMembers() to access $num_members! */

   /* Class constructor */
   function MySQLDB(){
      /* Make connection to database */
      $this->connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die(mysql_error());
      mysql_select_db(DB_NAME, $this->connection) or die(mysql_error());

      /**
       * Only query database to find out number of members
       * when getNumMembers() is called for the first time,
       * until then, default value set.
       */
      $this->num_members = -1;

      if(TRACK_VISITORS){
         /* Calculate number of users at site */
         $this->calcNumActiveUsers();

         /* Calculate number of guests at site */
         $this->calcNumActiveGuests();
      }
   }
   function getconfigs(){
      $arr = array();
   	  $q = "SELECT type, keyid, value FROM ipad_config";
      $result = mysql_query($q, $this->connection);
      while ($row = mysql_fetch_array($result)) {
      	$c = new config();
      	$c->type = $row[0];
      	$c->key = $row[1];
      	$c->value = $row[2];
        $arr[] = $c;
      }

      return $arr;
   }

   /**
    * addFormData - Inserts form data into the formstore table.
    */

   function addLocationItem($l){
	  $user = "sheri";
	  $lescape = mysql_escape_string($l);
   	  $q2 = "INSERT INTO tracker (`timestamp`, `user`, `location`)
	  		VALUES (".time().", '$user', '$lescape')";
	  error_log("AddLocationItem:".$q2, 0);

	  return mysql_query($q2, $this->connection);

   }

	function getFormsForPatient($pid){
      $arr = array();
   	  $q = "SELECT type, patient, formdata, createtimestamp, guid FROM formstore where patient = ".$pid;
      $result = mysql_query($q, $this->connection);
      while ($row = mysql_fetch_array($result)) {
      	$f = new visitdata();
      	$f->type = $row[0];
      	$f->patientid = $row[1];
      	$f->formdata = $row[2];
      	$f->createtimestamp = $row[3];
       	$f->guid = $row[4];
      	$arr[] = $f;
      }

      return $arr;
   }

   	function getFormsLockStatus($guid){
      $arr = array();
   	  $q = "SELECT locked FROM formstore where guid = '$guid'";
      $result = mysql_query($q, $this->connection);
      if ($row = mysql_fetch_array($result)) {
      	return $row[0];
      }
      return "0";
   }


	function getFormsAgencies(){
      $arr = array();
   	  $q = "SELECT DISTINCT agency FROM formstore order by agency";
      $result = mysql_query($q, $this->connection);
      while ($row = mysql_fetch_array($result)) {
      	$arr[] = $row[0];
      }

      return $arr;
   }

   	function getUnprocessedFormsCount(){
      $ret = 0;
   	  $q = "SELECT COUNT(id) FROM formstore where status = 'NEW'";
      $result = mysql_query($q, $this->connection);
      while ($row = mysql_fetch_array($result)) {
      	$ret = $row[0];
      }

      return $ret;
   }
 	function getFormsPatients(){
      global $database_ptiaccess;
      $arr = array();
   	  $q = "SELECT DISTINCT patientname, patient FROM formstore";
      $result = mysql_query($q, $this->connection);
      while ($row = mysql_fetch_array($result)) {
		$pid = $row[1];
      	$p = $database_ptiaccess->getpatient($pid);
      	$lastfirst = $p->lastname.$p->firstname;
      	$name = $row[0];
      	$arr[$lastfirst] = ($name);
      }
	  ksort($arr);
      return $arr;
   }
	function getConfigsForKey($key){
      $arr = array();
   	  $q = "SELECT `value` FROM `config` WHERE `key` = \"".$key."\" order by `value`";
      $result = mysql_query($q, $this->connection);
      while ($row = mysql_fetch_array($result)) {
      	$arr[] = $row[0];
      }

      return $arr;
   }



   /**
    * getallusers - returns array of users
    */
   function getallusers(){
      $q = "SELECT username, password, userlevel, employeeid FROM users";


      $result = mysql_query($q, $this->connection);
      $arr = array();

      while ($obj = mysql_fetch_array($result)) {
      	$arr[] = $obj;

      }

      return $arr;
   }
   function getuser($eid){
      $q = "SELECT username, password, userlevel, employeeid, fullname FROM users WHERE employeeid = $eid";


      $result = mysql_query($q, $this->connection);

      $obj = mysql_fetch_array($result);
      return $obj;
   }

   function geteid($username){
      $q = "SELECT employeeid FROM users where username = '$username'";


      $result = mysql_query($q, $this->connection);
	  $eid = 0;
      if ($obj = mysql_fetch_array($result)) {
      	$eid = $obj[0];
      }

      return $eid;
   }

   /**
    * confirmUserPass - Checks whether or not the given
    * username is in the database, if so it checks if the
    * given password is the same password in the database
    * for that user. If the user doesn't exist or if the
    * passwords don't match up, it returns an error code
    * (1 or 2). On success it returns 0.
    */
   function confirmUserPass($username, $password){
      /* Add slashes if necessary (for query) */
      if(!get_magic_quotes_gpc()) {
	      $username = addslashes($username);
      }

      /* Verify that user is in database */
      $q = "SELECT password FROM ".TBL_USERS." WHERE username = '$username'";
      $result = mysql_query($q, $this->connection);
      if(!$result || (mysql_numrows($result) < 1)){
         return 1; //Indicates username failure
      }

      /* Retrieve password from result, strip slashes */
      $dbarray = mysql_fetch_array($result);
      $dbarray['password'] = stripslashes($dbarray['password']);
      $password = stripslashes($password);

      /* Validate that password is correct */
      if($password == $dbarray['password']){
         return 0; //Success! Username and password confirmed
      }
      else{
         return 2; //Indicates password failure
      }
   }

   /**
    * confirmUserID - Checks whether or not the given
    * username is in the database, if so it checks if the
    * given userid is the same userid in the database
    * for that user. If the user doesn't exist or if the
    * userids don't match up, it returns an error code
    * (1 or 2). On success it returns 0.
    */
   function confirmUserID($username, $userid){
      /* Add slashes if necessary (for query) */
      if(!get_magic_quotes_gpc()) {
	      $username = addslashes($username);
      }

      /* Verify that user is in database */
      $q = "SELECT userid FROM ".TBL_USERS." WHERE username = '$username'";
      $result = mysql_query($q, $this->connection);
      if(!$result || (mysql_numrows($result) < 1)){
         return 1; //Indicates username failure
      }

      /* Retrieve userid from result, strip slashes */
      $dbarray = mysql_fetch_array($result);
      $dbarray['userid'] = stripslashes($dbarray['userid']);
      $userid = stripslashes($userid);

      /* Validate that userid is correct */
      if($userid == $dbarray['userid']){
         return 0; //Success! Username and userid confirmed
      }
      else{
         return 2; //Indicates userid invalid
      }
   }

   /**
    * usernameTaken - Returns true if the username has
    * been taken by another user, false otherwise.
    */
   function usernameTaken($username){
      if(!get_magic_quotes_gpc()){
         $username = addslashes($username);
      }
      $q = "SELECT username FROM ".TBL_USERS." WHERE username = '$username'";
      $result = mysql_query($q, $this->connection);
      return (mysql_numrows($result) > 0);
   }

   /**
    * usernameBanned - Returns true if the username has
    * been banned by the administrator.
    */
   function usernameBanned($username){
      if(!get_magic_quotes_gpc()){
         $username = addslashes($username);
      }
      $q = "SELECT username FROM ".TBL_BANNED_USERS." WHERE username = '$username'";
      $result = mysql_query($q, $this->connection);
      return (mysql_numrows($result) > 0);
   }

   /**
    * addNewUser - Inserts the given (username, password, email)
    * info into the database. Appropriate user level is set.
    * Returns true on success, false otherwise.
    */
   function addNewUser($username, $password, $email, $fullname, $ulevel, $id){
      $time = time();
      /* If admin sign up, give admin user level */
      //if(strcasecmp($username, ADMIN_NAME) == 0){
      //   $ulevel = ADMIN_LEVEL;
      //}else{
       //  $ulevel = USER_LEVEL;
      //}
      $q = "INSERT INTO ".TBL_USERS." VALUES ('$username', '$fullname', '$password', '0', $ulevel, '$email', $time, $id)";
      return mysql_query($q, $this->connection);
   }

   /**
    * updateUserField - Updates a field, specified by the field
    * parameter, in the user's row of the database.
    */
   function updateUserField($username, $field, $value){
      $q = "UPDATE ".TBL_USERS." SET ".$field." = '$value' WHERE username = '$username'";
      return mysql_query($q, $this->connection);
   }
   function updateFormStatus($id, $status){
      $q = "UPDATE `formstore` SET `status` = '$status' WHERE id = $id";
      error_log($q,0);
      return mysql_query($q, $this->connection);
   }

   function markNewCheckedAsProcessed() {
      $q = "UPDATE `formstore` SET `status` = 'PROCESSED' WHERE status = 'NEW' and checkstatus = 1";
      error_log($q,0);
      return mysql_query($q, $this->connection);


   }
   function markNewAsChecked() {
      $q = "UPDATE `formstore` SET `checkstatus` = 1 WHERE status = 'NEW' and checkstatus = 0";
      error_log($q,0);
      return mysql_query($q, $this->connection);


   }
   function updateFormLock($id, $lock){
      $q = "UPDATE `formstore` SET `locked` = $lock WHERE id = $id";
      return mysql_query($q, $this->connection);
   }
   function updateFormCheck($id, $check){
      $q = "UPDATE `formstore` SET `checkstatus` = $check WHERE id = $id";
      return mysql_query($q, $this->connection);
   }

   //this next one cleans up pateint names and sets them to last, first


   function updateformpatients() {
   	global $database_ptiaccess;
   	$q = "SELECT patient FROM formstore";
      $result = mysql_query($q, $this->connection);

      $arr = array();

      while ($row = mysql_fetch_array($result)) {
      	$arr[] = $row[0];
      }

	  foreach ($arr as $pid) {
		$p = $database_ptiaccess->getpatient($pid);
		$name = mysql_escape_string($p->lastname.", ".$p->firstname);
        $this->updatepatientname($pid, $name);
	  }

   }
   function updatepatientname($pid, $name) {
      	$q2 = "UPDATE `formstore` SET patientname = '$name' WHERE patient = $pid";
        return mysql_query($q2, $this->connection);
   }

   /**
    * getUserInfo - Returns the result array from a mysql
    * query asking for all information stored regarding
    * the given username. If query fails, NULL is returned.
    */
   function getUserInfo($username){
      $q = "SELECT * FROM ".TBL_USERS." WHERE username = '$username'";
      $result = mysql_query($q, $this->connection);
      /* Error occurred, return given name by default */
      if(!$result || (mysql_numrows($result) < 1)){
         return NULL;
      }
      /* Return result array */
      $dbarray = mysql_fetch_array($result);
      return $dbarray;
   }


   /**
    * getNumMembers - Returns the number of signed-up users
    * of the website, banned members not included. The first
    * time the function is called on page load, the database
    * is queried, on subsequent calls, the stored result
    * is returned. This is to improve efficiency, effectively
    * not querying the database when no call is made.
    */


   function getNumMembers(){
      if($this->num_members < 0){
         $q = "SELECT * FROM ".TBL_USERS;
         $result = mysql_query($q, $this->connection);
         $this->num_members = mysql_numrows($result);
      }
      return $this->num_members;
   }

   /**
    * calcNumActiveUsers - Finds out how many active users
    * are viewing site and sets class variable accordingly.
    */
   function calcNumActiveUsers(){
      /* Calculate number of users at site */
      $q = "SELECT * FROM ".TBL_ACTIVE_USERS;
      $result = mysql_query($q, $this->connection);
      $this->num_active_users = mysql_numrows($result);
   }

   /**
    * calcNumActiveGuests - Finds out how many active guests
    * are viewing site and sets class variable accordingly.
    */
   function calcNumActiveGuests(){
      /* Calculate number of guests at site */
      $q = "SELECT * FROM ".TBL_ACTIVE_GUESTS;
      $result = mysql_query($q, $this->connection);
      $this->num_active_guests = mysql_numrows($result);
   }

   /**
    * addActiveUser - Updates username's last active timestamp
    * in the database, and also adds him to the table of
    * active users, or updates timestamp if already there.
    */
   function addActiveUser($username, $time){
      $q = "UPDATE ".TBL_USERS." SET timestamp = '$time' WHERE username = '$username'";
      mysql_query($q, $this->connection);

      if(!TRACK_VISITORS) return;
      $q = "REPLACE INTO ".TBL_ACTIVE_USERS." VALUES ('$username', '$time')";
      mysql_query($q, $this->connection);
      $this->calcNumActiveUsers();
   }

   /* addActiveGuest - Adds guest to active guests table */
   function addActiveGuest($ip, $time){
      if(!TRACK_VISITORS) return;
      $q = "REPLACE INTO ".TBL_ACTIVE_GUESTS." VALUES ('$ip', '$time')";
      mysql_query($q, $this->connection);
      $this->calcNumActiveGuests();
   }

   /* These functions are self explanatory, no need for comments */

   /* removeActiveUser */
   function removeActiveUser($username){
      if(!TRACK_VISITORS) return;
      $q = "DELETE FROM ".TBL_ACTIVE_USERS." WHERE username = '$username'";
      mysql_query($q, $this->connection);
      $this->calcNumActiveUsers();
   }

   /* removeActiveGuest */
   function removeActiveGuest($ip){
      if(!TRACK_VISITORS) return;
      $q = "DELETE FROM ".TBL_ACTIVE_GUESTS." WHERE ip = '$ip'";
      mysql_query($q, $this->connection);
      $this->calcNumActiveGuests();
   }

   /* removeInactiveUsers */
   function removeInactiveUsers(){
      if(!TRACK_VISITORS) return;
      $timeout = time()-USER_TIMEOUT*60;
      $q = "DELETE FROM ".TBL_ACTIVE_USERS." WHERE timestamp < $timeout";
      mysql_query($q, $this->connection);
      $this->calcNumActiveUsers();
   }

   /* removeInactiveGuests */
   function removeInactiveGuests(){
      if(!TRACK_VISITORS) return;
      $timeout = time()-GUEST_TIMEOUT*60;
      $q = "DELETE FROM ".TBL_ACTIVE_GUESTS." WHERE timestamp < $timeout";
      mysql_query($q, $this->connection);
      $this->calcNumActiveGuests();
   }

   /**
    * query - Performs the given query on the database and
    * returns the result, which may be false, true or a
    * resource identifier.
    */
   function query($query){
      return mysql_query($query, $this->connection);
   }

};

/* Create database connection */
$database = new MySQLDB;

?>
