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
if (isset($_POST['submit'])) {
  // Retreive data from the input forms //
  $username = mysqli_real_escape_string($con, $_POST['username']);
  $password = mysqli_real_escape_string($con, $_POST['password']);
  $email= mysqli_real_escape_string($con, $_POST['email']);
  $vercode = mysqli_real_escape_string($con, $_POST['vercode']);
  $accid = mysqli_real_escape_string($con, $_POST['accid']);

  // Variable to store Error Message //
  $error = '';
  // The current time //
  date_default_timezone_set("Etc/GMT-8");
  $now = date("Y-m-d H:i:s");
  // Check the email is in use or not //
  $EMAIL = "SELECT * FROM user WHERE email = '$email'";
  $EMAILQ = mysqli_query($con, $EMAIL);
  // Check the username is in use or not //
  $USERNAME = "SELECT * FROM user WHERE username = '$username'";
  $USERNAMEQ = mysqli_query($con, $USERNAME);

  // Check the Verification Code used by anyone or not //
  // IF VERCODE has any data input //
  if (isset($_POST['vercode'])) {
    // Check the VERCODE has been used by user or not //
    $VERCODE = "SELECT * FROM user WHERE verification_code = '$vercode'";
    $VERCODEQ = mysqli_query($con, $VERCODE);
    // Check the database has the VERCODE being registered or not //
    $VERCODEV = "SELECT * FROM verification_code WHERE verification_code = '$vercode' AND code_used = 1";
    $VERCODEVQ = mysqli_query($con, $VERCODEV);
  }

  // Begin of the function: Reset Password //
    // if email not used by any user yet then //
    if (mysqli_num_rows($EMAILQ) > 0) {
      // if username is still available then //
      if (mysqli_num_rows($USERNAMEQ) > 0) {
        //***BEGIN OF PROCESS***//
        // initiate the verification of the VERCODE: Existence of VERCODE and the availability of it //
        if ((mysqli_num_rows($VERCODEVQ) > 0) && (mysqli_num_rows($VERCODEQ) > 0)) {
          // if the requirements were met, then we will start to take the user_id //
          $USERID = "SELECT * FROM user WHERE username = '".$username."' AND email = '".$email."' AND verification_code = '".$vercode."' AND account_code = '".md5($accid)."'";
          $USERIDQ = mysqli_query($con, $USERID);
          if (mysqli_num_rows($USERIDQ) < 1) {
              $error = 'ALERT: User Account can\'t be found, please try again.';
          } else {
            if ($row = mysqli_fetch_array($USERIDQ)) {
              $user_id = $row["user_id"];
            }
            // we begin to update the password with a new one //
            $RESETPW = "UPDATE user SET password = '".md5($password)."' WHERE user_id = '".$user_id."'";
            $RESETPWQ = mysqli_query($con, $RESETPW);
            if (mysqli_affected_rows($con) < 1) {
              echo "<script>alert('Reset Password failed.');</script>";
              $error = mysqli_error($con);
            } else {
              echo "<script>alert('Notice: Password reset-ed.');";
              echo "window.location.href='login.html';</script>";
            }
          }
        } elseif(mysqli_num_rows($VERCODEVQ) < 1) {
          $error = 'ALERT: Verification Code is invalid as the database can\'t found any registered user with the verification code, please try again.';
        } elseif (mysqli_num_rows($VERCODEQ) < 1) {
          $error = 'ALERT: System can\'t found any user with the verification code, please try again.';
        }
      } else {
        // if username has been used then //
        $error = 'NOTICE: Your username can\'t be found within the database, please ensure that you\'ve inputted the correct username.';
      }
    } else {
      // if email in use then //
      $error = 'NOTICE: Your Email can\'t be found within the database, please make sure you\'ve inputted the correct email.';
    }
  //***END OF PROCESS***//
}
// End of the function: Reset Password //
?>
