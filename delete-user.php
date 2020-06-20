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
	$DELETEUSER = "DELETE FROM user WHERE id = '".$_POST["id"]."'";
	if(mysqli_query($con, $DELETEUSER))
	{
		echo 'Data Deleted';
	}
?>
