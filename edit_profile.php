<?php
// we will only start the session with session_start() IF the session isn't started yet //
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<?php
// including the conn.php to establish connection with database //
  include "conn.php";
?>

<?php
// before we begin we have to check see the user's status, i.e. Student, Admin, or Cashier //
// first we define the database based on the type of user //
if ($_SESSION['status'] == 0) {
  $userdb = "student";
} else if ($_SESSION['status'] == 1) {
  $userdb = "cashier";
} else if ($_SESSION['status'] == 2) {
  $userdb = "admin";
} else {
  echo "<script>alert('Notice: Status of the user is undefined, please login and try again.');";
  echo "window.location.href='logout.php';</script>";
}
	$id = $_POST["id"];
	$text = $_POST["text"];
	$column_name = $_POST["column_name"];
	// now define the query depends on the type of column //
	// email requires verification that there's any other user using the same email address or not //
	if ($column_name == "email") {
		// we get the orginal email of the user first so we can check whether it's not a duplication or not //
		$CHECKMAIL = "SELECT * FROM user WHERE ".$column_name."='".$text."' AND user_id = '".$id."'";
		$CHECKMAILQ = mysqli_query($con, $CHECKMAIL);
		if (mysqli_num_rows($CHECKMAILQ) < 1) {
			// if there is none then we will check whether theres anyone else using the email address //
			$MAILUSE = "SELECT * FROM user WHERE ".$column_name."='".$text."'";
			$MAILUSEQ = mysqli_query($con, $MAILUSE);
			if (mysqli_num_rows($MAILUSEQ) < 1) {
				// if nobody is using the email then update it //
				$sql = "UPDATE user
								SET ".$column_name."='".$text."'
								WHERE user_id='".$id."'";
				if(mysqli_query($con, $sql))
				{
					echo 'Notice: Profile Info Updated.';
					$_SESSION[$column_name] = $_POST["text"];
				}
			} else {
				// else inform the user that there's already someone using the email //
				echo "Notice: Someone already using the email, please use another one to change your current email.";
			}
		} else {
			// if there is then the user must be currently using the input email //
			// since the input is the same as the email from the database, we do no alteration at all //
			$sql = "UPDATE user
							SET ".$column_name."='".$text."'
							WHERE user_id='".$id."'";
			if(mysqli_query($con, $sql))
			{
				echo 'Notice: Profile Info Updated.';
				$_SESSION[$column_name] = $_POST["text"];
			}
		}
	} else {
		$sql = "UPDATE `".$userdb."`
						SET ".$column_name."='".$text."'
						WHERE user_id='".$id."'";
		if(mysqli_query($con, $sql))
		{
			echo 'Notice: Profile Info Updated.';
			$_SESSION[$column_name] = $_POST["text"];
		}
	}

 ?>
