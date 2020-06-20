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
	$UPDATEBRAN = "UPDATE meal_brand SET ".$column_name."='".$text."' WHERE brand_id='".$id."'";
	if(mysqli_query($con, $UPDATEBRAN)) {
		echo 'Data Updated';
	}
?>
