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

class AccessDB
{
   var $connection;            /* Class constructor */
   function AccessDB(){
      /* Make connection to database */
      $this->connection = odbc_connect(ODBC_DSN, ODBC_USER, ODBC_PASS) or die("Could Not Aconnect To PTI Access DB");
   }
	function getagencies(){
      $arr = array();
   	  $q = "SELECT AgencyID, AgencyName FROM Agencies";
      $res = odbc_exec($this->connection, $q);
      $i = 0;
      while ($row = odbc_fetch_array($res)) {
        //if ($i > 10) {
        //	break 2;
       // }
      	//$i++;
      	$a = new agency();
      	$a->agencyid = $row["AgencyID"];
      	$a->agencyname = $row["AgencyName"];
        //$a->agencyid = $i;
      	//$a->agencyname = "Mike";
        $arr[] = $a;
      }	
      
      return $arr;
   }
   /**
    * getemployee - returns employee
    */
   function getemployee($employeeid){
      //$q = "SELECT FirstName, LastName, EmailName FROM Employees WHERE EMPLOYEEID = ".$employeeid;
      $q = "SELECT e.FirstName, e.LastName, e.EmailName, p.Prof, e.[Prof-id] FROM Employees e, 
      Profession p WHERE p.ProfID = e.[Prof-id] and EMPLOYEEID = ".$employeeid;
      $row = odbc_exec($this->connection, $q);
      
      $e = new employee();
      if (odbc_fetch_row($row)) {
      	$e->employeeid = $employeeid;
      	$e->firstname = odbc_result($row, 1);
      	$e->lastname = odbc_result($row, 2). ", " . strtoupper(odbc_result($row, 4));
		$e->email = odbc_result($row,3);
		$e->profid = odbc_result($row,5);
      
      }	
	  
      return $e;
   }
   function getemployeenamelastfirst($employeeid){
      //$q = "SELECT FirstName, LastName, EmailName FROM Employees WHERE EMPLOYEEID = ".$employeeid;
      $q = "SELECT e.FirstName, e.LastName, e.EmailName, p.Prof, e.[Prof-id] FROM Employees e, 
      Profession p WHERE p.ProfID = e.[Prof-id] and EMPLOYEEID = ".$employeeid;
      $row = odbc_exec($this->connection, $q);
      
      $name = "";
      if (odbc_fetch_row($row)) {
      	$name = odbc_result($row, 2).", ".odbc_result($row, 1);
      }	
	  
      return $name;
   }
   function getpatient($patientid){
     $q = "SELECT ContactFirstName, ContactLastName, PhoneNumber, 
         Address, CityName, Customers.State, ZipCode,
		 KMap, KMapLetter, KMapNote, AgencyId, DateOfBirth, FamilyContactNameLastName, InstructionToEmployee 
		 FROM Customers, Cities WHERE Cities.CityID = Customers.CityID AND CustomerID = ".$patientid;
      $row = odbc_exec($this->connection, $q);
      
      $p = new customer();
      if (odbc_fetch_row($row)) {
      	$p->customerid = $patientid;  
  		$p->firstname = odbc_result($row, 1);
   		$p->lastname = odbc_result($row, 2);   
   		$p->phone = odbc_result($row, 3);
   		$p->address = odbc_result($row, 4);
   		$p->city = odbc_result($row, 5);
   		$p->state = odbc_result($row, 6);
   		$p->zip = odbc_result($row, 7);
   		$p->kmap = odbc_result($row, 8);
   		if (strlen($p->kmap) == 0) $p->kmap = "na";
   		$p->kmapletter = odbc_result($row, 9);
   		if (strlen($p->kmapletter) == 0) $p->kmapletter = "na";
   		$p->kmapnote = odbc_result($row, 10);
   		if (strlen($p->kmapnote) == 0) $p->kmapnote = "na";
   		$p->agencyid = odbc_result($row, 11);

   		$tdate = odbc_result($row, 12);  		
   		if (odbc_result($row, 12) != NULL) {
    		$rdate = DateTime::createFromFormat("Y-m-d H:i:s", odbc_result($row, 12));
   			$tmpBirthDate = $rdate->format('y');
   			$tmpThisYear = date('y');
   			if ($tmpBirthDate > $tmpThisYear && $tmpBirthDate < 30) {
   				$newYear = '19'.$tmpBirthDate;
   				$p->birthdate = $rdate->format('m/d/').$newYear;
   			} else {
    			$p->birthdate = $rdate->format('m/d/Y');  			
   				
   			}
   		} else {

   			$p->birthdate = ""; 
   		}
   		if (odbc_result($row, 13) != NULL) {
   			$p->altphone = odbc_result($row, 13);
   		} else {
   			$p->altphone = "";
   			
   		}
   		//$p->instructions = " ";
   		//$a = getAgency($p->agencyid);
   		$q = "SELECT PhoneNumberAgency
   	  		FROM Agencies  
   	  		WHERE AgencyID = ".$p->agencyid;
      	$res = odbc_exec($this->connection, $q);
      	$agencyPhone = "";
      	if ($row2 = odbc_fetch_array($res)) {
      		$agencyPhone = $row2["PhoneNumberAgency"];
      	}	
	  
   		if (odbc_result($row, 14) != NULL) {
   		
   			$instr = odbc_result($row, 14);
   			$instrDecode = str_replace(chr(146), "'", $instr);
   			$p->instructions = "(Ag#:".$agencyPhone.")".$instrDecode;
   		} else {
   			$p->instructions = "(Ag#:".$agencyPhone.")";	
   		}
   		
   		
      }	
	  
      return $p;
   }

    /**
    * getallpatients - returns employee
    */
   function getallpatients($search){
      //$q = "SELECT CustomerID, ContactFirstName, ContactLastName from Customers where ContactLastName Like '".$search."%' and CustomerID IN (SELECT TOP 2000 CustomerID FROM Customers ORDER BY CustomerID DESC) ORDER BY ContactLastName";
   	  //$q = "SELECT TOP 2000 CustomerID, ContactFirstName, ContactLastName from Customers where ContactLastName Like '".$search."%' order by ContactFirstName";
   	  $q =  "SELECT DISTINCT  TOP 2000 Customers.ContactFirstName, Customers.ContactLastName, Customers.CustomerID "
			."FROM Customers INNER JOIN [Order Details] ON Customers.CustomerID = [Order Details].CustomerId "
			."WHERE (((Customers.ContactLastName) Like '".$search."%') AND (([Order Details].StatusId)<>2)) "
			."ORDER BY Customers.ContactLastName, Customers.ContactFirstName";
   	  if (is_numeric($search)) {
   	  	$q = "SELECT TOP 2000 CustomerID, ContactFirstName, ContactLastName from Customers where CustomerID = $search";
      } 
   		//  error_log("getallpatients:".$q, 0);
      $res = odbc_exec($this->connection, $q);
      $arr = array();
      while ($row = odbc_fetch_array($res)) {
      	$p = new customerlight();
		$p->customerid = $row["CustomerID"];
		$p->firstname = $row["ContactFirstName"]." [".$p->customerid."]";
		$p->lastname = $row["ContactLastName"];
        $arr[] = $p;
      }	
	  
      return $arr;
   }
   
   
   
   /**
    * getallemployees - returns employee
    */
   function getallemployees(){
      $q = "SELECT EMPLOYEEID, FirstName, LastName, EmailName, Prof-id FROM Employees ORDER BY LastName";
      $res = odbc_exec($this->connection, $q);
      $arr = array();
      while ($row = odbc_fetch_array($res)) {
      	$e = new employee();
      	$e->employeeid = $row["EMPLOYEEID"];
      	$e->firstname = $row["FirstName"];
      	$e->lastname = $row["LastName"];
      	$e->email = $row["EmailName"];
      	$e->profid = $row["Prof-id"];
        $arr[] = $e;
      }	
	  
      return $arr;
   }
   function getconfigs(){
      $arr = array();
   	  $q = "SELECT TypeVisitID, TypeVisitName FROM TypeOfVisits ORDER BY TypeVisitID";
      $res = odbc_exec($this->connection, $q);
      while ($row = odbc_fetch_array($res)) {
      	$c = new config();
      	$c->type = "VISIT_TYPE";
      	$c->key = $row["TypeVisitID"];
      	$c->value = $row["TypeVisitName"];
        $arr[] = $c;
      }	
      
   	  $q = "SELECT TherapyID, TherapyName FROM Therapies ORDER BY TherapyID";
      $res = odbc_exec($this->connection, $q);
      while ($row = odbc_fetch_array($res)) {
      	$c = new config();
      	$c->type = "THERAPY_TYPE";
      	$c->key = $row["TherapyID"];
      	$c->value = $row["TherapyName"];
        $arr[] = $c;
      }	
      
	  
      return $arr;
   }
   
   function getscheduleforemployee($eid){

	  //$start_date = "02/01/2010";
	  $start_date = date ('m/d/Y');
   	
   	  $newdate = strtotime ( '+10 day' , strtotime ( $start_date) ) ;
	  $end_date = date ( 'm/d/Y' , $newdate );
	  
   	  $q = "SELECT VisitDateProject, CustomerId, StatusID, StatusComment, TherapyId, TypeVisitId, FirstVisit FROM [Order Details] WHERE VisitDateProject >= #$start_date# AND  VisitDateProject <= #$end_date# AND employeeid = $eid ORDER BY VisitDateProject";
   	  		
   	  $res = odbc_exec($this->connection, $q);
      $arr = array();
      while ($row = odbc_fetch_array($res)) {
      	$v = new visit();
      	$v->customerid = $row["CustomerId"];
      	$v->employeeid = $eid;
      	$v->firstvisit = $row["FirstVisit"];
      	$v->visitdate = date ('m/d/Y', strtotime($row["VisitDateProject"]));
      	$v->visittype = $row["TypeVisitId"];
      	$v->therapytypeid = $row["TherapyId"];
        $arr[] = $v;
      }	
	  
   	  $q2 = "SELECT VisitDateProject, CustomerId, StatusID, StatusComment, TherapyId, TypeVisitId, FirstVisit FROM [Order Details] WHERE VisitDateProject >= #$start_date# AND  VisitDateProject <= #$end_date# AND AssistantNameId = $eid ORDER BY VisitDateProject";
   	  		
   	  $res2 = odbc_exec($this->connection, $q2);
      while ($row = odbc_fetch_array($res2)) {
      	$v = new visit();
      	$v->customerid = $row["CustomerId"];
      	$v->employeeid = $eid;
      	$v->firstvisit = $row["FirstVisit"];
      	$v->visitdate = date ('m/d/Y', strtotime($row["VisitDateProject"]));
      	$v->visittype = $row["TypeVisitId"];
      	$v->therapytypeid = $row["TherapyId"];
        $arr[] = $v;
      }	
      
      
      return $arr;
   }

      function getcustomer($cid){

   	  $q = "SELECT c.ContactFirstName, c.ContactLastName, 
   	  		c.Address, c.CityId, c.ZipCode, c.State,
   	  		c.KMap, c.KMapLetter, c.KMapNote, c.PhoneNumber,
   	  		c.AgencyId, c.DateOfBirth  
   	  		FROM Customers c  
   	  		WHERE CustomerID = $cid";
      $row = odbc_exec($this->connection, $q);
      //$arr = array();
      //while ($row = odbc_fetch_array($res)) {
      $c = new customer();
      if (odbc_fetch_row($row)) {
       	$c->customerid = $cid;
      	$c->firstname = odbc_result($row, 1);
      	$c->lastname = odbc_result($row, 2);
      	$c->address = odbc_result($row, 3);
      	$c->city = odbc_result($row, 4);
      	$c->zip = odbc_result($row, 5);
      	$c->state = odbc_result($row, 6);
      	$c->kmap = odbc_result($row, 7);
      	$c->kmapletter = odbc_result($row, 8);
      	$c->kmapnote = odbc_result($row, 9);
      	$c->phone = odbc_result($row, 10);
      	$c->agencyid = odbc_result($row, 11);

        //$tdate = "";
   		$tdate = odbc_result($row, 12);  		
   		if (odbc_result($row, 12)) {
    		$rdate = DateTime::createFromFormat("Y-m-d H:i:s", odbc_result($row, 12));
   			$tmpBirthDate = $rdate->format('y');
   			$tmpThisYear = date('y');
   			if ($tmpBirthDate > $tmpThisYear && $tmpBirthDate < 30) {
   				$newYear = '19'.$tmpBirthDate;
   				$c->birthdate = $rdate->format('m/d/').$newYear;
   			} else {
    			$c->birthdate = $rdate->format('m/d/Y');  			
   				
   			}
    		
   		} else {

   			$c->birthdate = ""; 
   		}
   		
      
      }	
	  
      return $c;
   }

   function patientActive($cid){
	  /*
   	  $q = "SELECT orderdetailsid  
   	  		FROM [Order Details]
   	  		WHERE StatusID <> 2 and CustomerID = $cid";
   	  */
   	  $active = true;
   	  /*
   	  $q = "SELECT top 1 StatusID
   	  		FROM [Order Details]
   	  		WHERE  CustomerID = $cid
   	  		and VisitDateProject < '1/1/3000'    	  		
			order by visitdateproject";
   	  */
   	  
  	  $q = "SELECT top 1 StatusID, orderdetailsid
   	  		FROM [Order Details]
   	  		WHERE  CustomerID = $cid
   	  		and firstvisit = 1
   	  		and TherapyId = 1 
			order by datein DESC";
   	  
      $row = odbc_exec($this->connection, $q);
      if (odbc_fetch_row($row)) {
  		 $status = odbc_result($row, 1);
      
	      if ($status == 2) {
			 $active = false;		
		  } 	
      }
      return $active;
   }
   
      function getagency($aid){

   	  $q = "SELECT AgencyName, ContactNameAgency, ContactLastNameAgency, AgencyName, LocationAgency,
   	  		AddressAgency, CityAgency, PostalCodeAgency, FaxNumberAgency, PhoneNumberAgency, StateAgency  
   	  		FROM Agencies  
   	  		WHERE AgencyID = $aid";
      $res = odbc_exec($this->connection, $q);
      $arr = array();
      while ($row = odbc_fetch_array($res)) {
      	$a = new agency();
      	$a->agencyid = $aid;
      	$a->agencyid = $row["AgendcyId"];
      	$a->agencyname = $row["AgencyName"];
      	$a->firstname = $row["ContactNameAgency"];
      	$a->lastname = $row["ContactLastNameAgency"];
      	$a->address = $row["AddressAgency"];
      	$a->location = $row["LocationAgency"];
      	$a->zip = $row["PostalCodeAgency"];
      	$a->city = $row["CityAgency"];
      	$a->fax = $row["FaxNumberAgency"];
      	$a->phone = $row["PhoneNumberAgency"];
      	$a->state = $row["StateAgency"];
      	$arr[] = $a;
      }	
	  
      return $arr;
   }
   
   
   
   

	function addScheduleItem($cid,$eid,$therapytype,$visittype,$visitdate){
   	  $success = true;

   	  $q = "INSERT INTO [Order Details]  
				(CustomerId,TherapyId,TypeVisitId,VisitDate,TransferEmployeeId,VisitDateProject,StatusId,
				StatusDCNoteDate,FileNumber,RecertDate,EmployeeId,AssistantNameId,WeekDayAux,
				WeekDayAuxProjected,FirstVisit,Invoiceable,CompanyID,InvoiceNumber,DateIn,InvoiceDate) 
				VALUES 
				($cid,$therapytype,$visittype,'01/01/3000',0,'$visitdate',1,
				'01/01/3000',0,'01/01/3000',$eid,0,0,
				0,0,1,1,0,'01/01/4000','01/01/3000') ";  
		$objQuery = odbc_exec($this->connection, $q);  
		if(!$objQuery)  
		{  
			$success = false;
			error_log("Schedule Insert Fail:".odbc_error(), 0);
			
			//echo "Error Save [".odbc_error()."]";  
		}  
   	  
      return $success;
   	}
   
	function addFormUploadRecord($jsondata){
   	  $success = true;
 	  $decoded = json_decode($jsondata, true);
 	  $visittypetxt = $decoded['TYPE'];
 	  if ($visittypetxt == "note" || $visittypetxt == "eval" || $visittypetxt == "re-eval") {
	 	  $visittype = 2;
	 	  if ($visittypetxt == "note") {
	 	  	$visittype = 4;
	 	  }
	 	  if ($visittypetxt == "re-eval") {
	 	  	$visittype = 7;
	 	  }
	 	  $cid = $decoded['PATIENTID'];
	 	  $eid = $decoded['EID'];
	 	  $therapytype = $decoded['PT'];  //FIX THIS WHEN WE MOVE TO MULTI THERAPY TYPES
	 	  $visitdate = $decoded['EVALDATE'];
	   	  $q = "INSERT INTO [Order Details_ipad_VISITS]  
					(CustomerId,TherapyId,TypeVisitId,VisitDate,TransferEmployeeId,VisitDateProject,StatusId,
					StatusDCNoteDate,FileNumber,RecertDate,EmployeeId,AssistantNameId,WeekDayAux,
					WeekDayAuxProjected,FirstVisit,Invoiceable,CompanyID,InvoiceNumber,DateIn,InvoiceDate) 
					VALUES 
					($cid,$therapytype,$visittype,'$visitdate',0,'01/01/3000',1,
					'01/01/3000',0,'01/01/3000',$eid,0,0,
					0,0,1,1,0,'01/01/3000','01/01/3000') ";  
			$objQuery = odbc_exec($this->connection, $q);  
			if(!$objQuery)  
			{  
				$success = false;
				error_log("Schedule Insert Fail:".odbc_error(), 0);
				
				//echo "Error Save [".odbc_error()."]";  
			}  
	   	  
	      return $success;
	   	
		} else {
			return false;
		} 
	}

};

/* Create database connection */
$database_ptiaccess = new AccessDB;

?>
