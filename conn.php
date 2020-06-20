<?php
$con = mysqli_connect("localhost","root","","meal_debit_system");

// Check connection
if(mysqli_connect_error())
 {
  echo "Failed to connect to MySQL:".mysqli_connect_error();
  // To prevent header error (it will occur randomly), we will need to use ob_start() and ob_end_flush() function //
  ob_start();
  // It will return to homepage automatically (10 seconds later) if SQL error occured //
  header("refresh:10; url=homepage.html");
  ob_end_flush();
  exit();
 }
?>
