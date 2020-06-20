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
  $username = mysqli_real_escape_string($con, $_POST['username']);
  $password = mysqli_real_escape_string($con, $_POST['password']);
  // Taking the current time //
  date_default_timezone_set("Etc/GMT-8");
  $now = date("Y-m-d H:i:s");
  // Variable to store Error Message //
  $error = '';
  // Sorting out the query related to the function //
  $LOGIN = "SELECT * FROM user WHERE username = '".$username."' AND password = '".md5($password)."'";
  $LOGINQ = mysqli_query($con, $LOGIN);

  //***BEGIN OF PROCESS***//
  if (mysqli_num_rows($LOGINQ) < 1) {
      $error = 'ALERT: Username or Password is invalid, please try it again.';
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
            $LASTLOGIN = "UPDATE user SET last_login = '".$now."' WHERE username = '".$username."' AND password = '".md5($password)."'";
            $LASTLOGINQ = mysqli_query($con, $LASTLOGIN);

            // Status which is 0 will be for student //
            if ($_SESSION['status'] === "0") {
              $STUDENT = "SELECT * FROM student WHERE user_id = '".$_SESSION['user_id']."'";
              $STUDENTQ = mysqli_query($con, $STUDENT);
              if (mysqli_num_rows($STUDENTQ) < 1) {
                // Alert the user that there's no info about the account, and immediately session_destroy(), and revert back to homepage //
                session_destroy();
                ob_start();
                // It will return to homepage automatically //
                echo "<script>alert('ALERT: Account Info couldn\'t found, will immediately terminate the process and return to homepage.');";
                echo "window.location.href='homepage.html';</script>";
                ob_end_flush();
                exit();
              } else {
                // If there are information existed inside the database then will begin the process of taking in the info //
                  if ($row = mysqli_fetch_array($STUDENTQ)) {
                    // Then we get the data from the database //
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
                    echo "<script>alert('Welcome back, ".$_SESSION['role']." ".$_SESSION['last_name'].".');";
                    echo "window.location.href='homepage.html';</script>";
                  }
              }
            } else if ($_SESSION['status'] === "1") {
              // Status which is 1 will be for cashier //
              // Will begin the process with the procedure likable above //
                $CASHIER = "SELECT * FROM cashier WHERE user_id = '".$_SESSION['user_id']."'";
                $CASHIERQ = mysqli_query($con, $CASHIER);
                if (mysqli_num_rows($CASHIERQ) < 1) {
                  // Alert the user that there's no info about the account, and immediately session_destroy(), and revert back to homepage //
                  session_destroy();
                  ob_start();
                  // It will return to homepage automatically //
                  echo "<script>alert('ALERT: Account Info couldn\'t found, will immediately terminate the process and return to homepage.');";
                  echo "window.location.href='homepage.html';</script>";
                  ob_end_flush();
                  exit();
                } else {
                  // If there are information existed inside the database then will begin the process of taking in the info //
                    if ($row = mysqli_fetch_array($CASHIERQ)) {
                      // Then we get the data from the database //
                      $_SESSION['first_name'] = $row['first_name'];
                      $_SESSION['last_name'] = $row['last_name'];
                      $_SESSION['gender'] = $row['gender'];
                      $_SESSION['dob'] = $row['dob'];
                      $_SESSION['admin_approved'] = $row['admin_approved'];

                      // Now taking in Profile Image, if the user has none then the system will use the default image //
                      if (isset($row['profile_image'])) {
                        $_SESSION['profile_image'] = $row['profile_image'];
                      } else {
                        $_SESSION['profile_image'] = "/APU/SDP/image/01.png";
                      }

                      // defining the $_SESSION for the transaction purpose //
                      $_SESSION['order_id'] = "";
                      $_SESSION['buyer_id'] = "";
                      $_SESSION['brand_id'] = "";

                      // And declaring the role of the account as Student //
                      $_SESSION['role'] = "Cashier";

                      // Then we will welcome the user according to the status //
                      echo "<script>alert('Welcome back, ".$_SESSION['role']." ".$_SESSION['last_name'].".');";
                      echo "window.location.href='cashier_page.html';</script>";
                    }
                }
            } else if ($_SESSION['status'] === "2") {
              // Status which is 2 will be for admin //
              // Will begin the process with the procedure likable above //
              $ADMIN = "SELECT * FROM admin WHERE user_id = '".$_SESSION['user_id']."'";
              $ADMINQ = mysqli_query($con, $ADMIN);
              if (mysqli_num_rows($ADMINQ) < 1) {
                // Alert the user that there's no info about the account, and immediately session_destroy(), and revert back to homepage //
                session_destroy();
                ob_start();
                // It will return to homepage automatically //
                echo "<script>alert('ALERT: Account Info couldn't found, will immediately terminate the process and return to homepage.');";
                echo "window.location.href='homepage.html';</script>";
                ob_end_flush();
                exit();
              } else {
                // If there are information existed inside the database then will begin the process of taking in the info //
                  if ($row = mysqli_fetch_array($ADMINQ)) {
                    // Then we get the data from the database //
                    $_SESSION['first_name'] = $row['first_name'];
                    $_SESSION['last_name'] = $row['last_name'];
                    $_SESSION['gender'] = $row['gender'];

                    // Now taking in Profile Image, if the user has none then the system will use the default image //
                    if (isset($row['profile_image'])) {
                      $_SESSION['profile_image'] = $row['profile_image'];
                    } else {
                      $_SESSION['profile_image'] = "/APU/SDP/image/02.gif";
                    }

                    // And declaring the role of the account as Student //
                    $_SESSION['role'] = "Admin";
                    // Then we will welcome the user according to the status //
                    echo "<script>alert('Welcome back, ".$_SESSION['role']." ".$_SESSION['last_name'].".');";
                    echo "window.location.href='admin_panel.html';</script>";
                  }
              }
            } else {
              // If the status value is not all the above, then something is wrong //
              // With that we will destroy the session and force the user back to the homepage just incase of security //
              // To prevent header error (it will occur randomly), we will need to use ob_start() and ob_end_flush() function //
              session_destroy();
              echo "<script>alert('ALERT: Status undefined, please do contact with the Administration for further info.');";
              echo "window.location.href='homepage.html';</script>";
              exit();
            }
        } else {
          // If the there's a reason stating why the user got suspended, then will redirect to a webpage to show the user that their account being suspended and state the reason of it //
          if ((isset($row['suspended_reason'])) && (($row['suspended_reason']) != NULL))  {
              $_SESSION['suspender_admin'] = "ADMIN";
              $_SESSION['suspended_reason'] = $row['suspended_reason'];
          } else {
              $_SESSION['suspender_admin'] = "DEFALT";
              $_SESSION['suspended_reason'] = "Reason has yet to be confirmed. Please do contact the Administration.";
          }
          echo "<script>alert('Notice: Your account is suspended due to some reason.');";
          echo "window.location.href='account_restricted.html';</script>";
        }
      }
  }
  //***END OF PROCESS***//
}
// End of the function: Login //
?>
