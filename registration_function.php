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
  $fname = mysqli_real_escape_string($con, $_POST['first_name']);
  $lname = mysqli_real_escape_string($con, $_POST['last_name']);
  $gender = mysqli_real_escape_string($con, $_POST['gender']);
  $dob = mysqli_real_escape_string($con, $_POST['dob']);
  $vercode = mysqli_real_escape_string($con, $_POST['vercode']);
  $accid = mysqli_real_escape_string($con, $_POST['accid']);

  // Variable to store Error Message //
  $error = '';
  // Calculate Age //
  $present = date_create();
  $inputdate = date_create($dob);
  $age = date_diff($present, $inputdate);
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
    $VERCODE = "SELECT * FROM registration_list WHERE verification_code = '$vercode'";
    $VERCODEQ = mysqli_query($con, $VERCODE);
    // Check the database has the VERCODE being registered or not //
    $VERCODEV = "SELECT * FROM verification_code WHERE verification_code = '$vercode' AND code_used = 0";
    $VERCODEVQ = mysqli_query($con, $VERCODEV);
  }

  // Begin of the function: Registration //

  // if the age is over 18 YO then //
  if (($age->y) > 18 ) {
    // if email not used by any user yet then //
    if (mysqli_num_rows($EMAILQ) < 1) {
      // if username is still available then //
      if (mysqli_num_rows($USERNAMEQ) < 1) {
        //***BEGIN OF PROCESS***//
          // initiate the verification of the VERCODE: Existence of VERCODE and the availability of it //
          if ((mysqli_num_rows($VERCODEVQ) > 0) && (mysqli_num_rows($VERCODEQ) < 1)) {
            // if the requirements were met, then we put the information into the registration list in database so the Admins are able to verify and register them //
            $query = "INSERT INTO registration_list (`username`, `password`, `verification_code`, `account_id`, `first_name`, `last_name`, `gender`, `dob`, `email`, `verified`, `apply_date`)
                      VALUES ('$username', '".md5($password)."','$vercode','$accid','$fname','$lname','$gender','$dob','$email','0','$now');";
            // and at the same time we will update the VERCODE database, by updating the vercode is already been used //
            $query .= "UPDATE verification_code SET code_used = '1', used_date = '$now' WHERE verification_code = '$vercode'";
            if (mysqli_multi_query($con, $query)) {
              do {
                if ($sql_result = mysqli_store_result($con)) {
                  // Fetch one and another row //
                  while ($row = mysqli_fetch_row($sql_result)) {
                    printf("%s\n",$row[0]);
                  }
                  // Free result set //
                  mysqli_free_result($sql_result);
                }
              }
              while (mysqli_more_results($con) && mysqli_next_result($con));
              if (mysqli_affected_rows($con) < 1) {
                $error = mysqli_error($con);
              } else {
                echo "<script>alert('Registration Succeed. Please wait around 3 days to have the Admins verify your account.');";
                echo "window.location.href='homepage.html';</script>";
              }
            }
          } elseif(mysqli_num_rows($VERCODEVQ) < 1) {
            $error = 'ALERT: Verification Code is invalid, please try again or contact with your Administrator.';
          } elseif (mysqli_num_rows($VERCODEQ) > 0) {
            $error = 'ALERT: There is a user already using this Verification Code, please try another one.';
          }
      } else {
        // if username has been used then //
        $error = 'NOTICE: Your Username already been used, please use another Username.';
      }
    } else {
      // if email in use then //
      $error = 'NOTICE: Your Email has been used. Please use another one.';
    }
  } else {
    // if less than 18 years old then //
    $error = 'NOTICE: Your age is under 18 years old, sorry, no kids allowed.';
  }
  //***END OF PROCESS***//
}
// End of the function: Registration //
?>
