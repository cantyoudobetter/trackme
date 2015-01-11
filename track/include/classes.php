<?php
class visit
{
   public $employeeid;  
   public $customerid;   
   public $therapytypeid;   
   public $visitdate;   
   public $firstvisit; 
   public $visittype;
   
}
class visitdata
{
   public $type;  
   public $patientid;  
   public $createtimestamp;   
   public $guid;  
   public $formdata;   
}

class customer
{
   public $customerid;  
   public $firstname;   
   public $lastname;   
   public $phone;   
   public $address; 
   public $city;
   public $state;
   public $zip;
   public $kmap;
   public $kmapletter;
   public $kmapnote;
   public $agencyid;
   public $birthdate;
   public $altphone;
   public $instructions;   
}
class customerlight
{
   public $customerid;  
   public $firstname;   
   public $lastname;         
}

class agency
{
   public $agencyid;  
   public $agencyname;
      
}

class employee
{
   public $employeeid;  
   public $firstname;   
   public $lastname; 
   public $email;
   public $profid;
   
}
class user
{
   public $username;  
   public $password;   
   public $firstname; 
   public $lastname;
   public $userlevel; 
   public $email;
   public $employeeid;
   public $profid;
   
}

class config
{
   public $type;
   public $key;  
   public $value;   
}

?>