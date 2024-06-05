<?php
 
   $server ="srv544.hstgr.io";
   $username="u745359346_WDIAPR24T5";
   $password="WDIAPR24Team5.Calanjiyam@2024";
   $db="u745359346_WDIAPR24T5";

   $con = mysqli_connect($server,$username,$password,$db);

   if(!$con){

       die("connection to this database failed due to".mysqli_connect_error());
   }
       echo "Connection successfull";

?>