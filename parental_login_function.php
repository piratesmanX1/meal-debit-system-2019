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
// Begin of the function: Login //

// First we check the form has submitted or not //
if (isset($_POST['submit'])) {
  // If it is, then we will retreive data from the input forms //
  $email = mysqli_real_escape_string($con, $_POST['email']);
  $username = mysqli_real_escape_string($con, $_POST['username']);
  $access_code = mysqli_real_escape_string($con, $_POST['access_code']);
  // Taking the current time //
  date_default_timezone_set("Etc/GMT-8");
  $now = date("Y-m-d H:i:s");
  // Variable to store Error Message //
  $error = '';
  // Sorting out the query related to the function //
  $LOGIN = "SELECT * FROM user INNER JOIN student
            ON user.user_id = student.user_id
            WHERE user.email = '".$email."' AND user.username = '".$username."' AND student.access_code = '".md5($access_code)."' AND user.active = 1 AND user.status = 0";
  $LOGINQ = mysqli_query($con, $LOGIN);

  //***BEGIN OF PROCESS***//
  if (mysqli_num_rows($LOGINQ) < 1) {
      $error = 'ALERT: Username, Email, or Access Code is invalid, please try it again.';
  } else {
      if ($row = mysqli_fetch_array($LOGINQ)) {
          // First we check the account is active or not //
          $_SESSION['active'] = $row['active'];
          // If it's active then we will update the time of login into the database, but we get the data of when the actual last login time of the account first before update //
          $_SESSION['last_login'] = $row['last_login'];

          if ($_SESSION['active'] > 0) {
            // Then we get the data from the database //
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['account_id'] = $row['account_id'];
            $_SESSION['status'] = $row['status'];
            $_SESSION['registered_date'] = $row['registered_date'];

            // Then we will start obtaininng information of the account, depening of the account status //
            //$LASTLOGIN = "UPDATE user SET last_login = '".$now."' WHERE username = '".$username."' AND password = '".md5($password)."'";
            //$LASTLOGINQ = mysqli_query($con, $LASTLOGIN);
            // P.S: Discarded as Parent's login should be remained anonymous //

            $_SESSION['first_name'] = $row['first_name'];
            $_SESSION['last_name'] = $row['last_name'];
            $_SESSION['gender'] = $row['gender'];
            $_SESSION['dob'] = $row['dob'];
            $_SESSION['admin_approved'] = $row['admin_approved'];
            // $_SESSION['balance'] = $row['balance']; //
            // Note: Considering making balance a real-life factor which always sync with the database to improve the effectiveness //

            // Now taking in Profile Image, if the user has none then the system will use the default image //
            if (isset($row['profile_image'])) {
              $_SESSION['profile_image'] = $row['profile_image'];
            } else {
              $_SESSION['profile_image'] = "/APU/SDP/image/00.png";
            }

            // And declaring the role of the account as Student //
            $_SESSION['role'] = "Student";
            // Then we will welcome the user according to the status //
            echo "<script>alert('Welcome back, ".$_SESSION['role']." ".$_SESSION['last_name'].". P.S: I know you are not the student himself. ');";
            echo "window.location.href='homepage.html';</script>";
        } else {
          // If the there's a reason stating why the user got suspended, then will redirect to a webpage to show the user that their account being suspended and state the reason of it //
          if (isset($row['suspended_reason']))  {
              $_SESSION['suspender_admin'] = "ADMIN";
              $_SESSION['suspended_reason'] = $row['suspend_reason'];
          } else {
              $_SESSION['suspender_admin'] = "DEFALT";
              $_SESSION['suspended_reason'] = "Reason has yet to be confirmed. Please do contact the Administration.";
          }
          ob_start();
          header("Location: account_restricted.html");
          ob_end_flush();
          exit();
        }
      }
  }
  //***END OF PROCESS***//
}
// End of the function: Login //
?>
