<?php
// we will only start the session with session_start() IF the session isn"t started yet //
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<?php
// including the conn.php to establish connection with database //
  include "conn.php";
?>

<?php
	$id = $_POST["id"];
	$text = $_POST["text"];
	$column_name = $_POST["column_name"];
	$UPDATEMEAL = "UPDATE meal SET ".$column_name."='".$text."' WHERE meal_id='".$id."'";
	if(mysqli_query($con, $UPDATEMEAL)) {
		echo 'Data Updated';
	}
?>
