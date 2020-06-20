<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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
// Begin of the function: Verification Code's Verification Form: Disable //
// First we check the form has submitted or not //
if (isset($_POST['submit-list-disA'])) {
  // If it is, then we will retreive data from the input forms //
  $verid = $_POST["verid"];
  $dis_acccode = mysqli_real_escape_string($con, $_POST['dis-acccode']);
  $dis_pw = mysqli_real_escape_string($con, $_POST['dis-pw']);
  // Taking the current time //
  date_default_timezone_set("Etc/GMT-8");
  $now = date("Y-m-d H:i:s");
  // Variable to store Error Message //
  $error = '';

  // Sorting out the query related to the function //
  // Verify the user is an admin or not //
  $VERFYADMIN = "SELECT * FROM user
                 WHERE status = 2 AND active = 1 AND account_code = '".md5($dis_acccode)."' AND password = '".md5($dis_pw)."'";
  $VERFYADMINQ = mysqli_query($con, $VERFYADMIN);

  //***BEGIN OF PROCESS***//
  if (mysqli_num_rows($VERFYADMINQ) < 1) {
    // if the admin is not verified, then inform the user and send him back to admin panel //
    echo "<script>alert('ALERT: Information unable to be verified. Please try again.');";
    echo "window.location.href='admin_panel.html';</script>";
    exit(0);
  } else {
    // begin the process of disabling the verification code //
    while (list($key,$val) = @each ($verid)) {
      // first we check the verification code exists in database or not //
      $VERCODEEX = "SELECT * FROM verification_code
                    WHERE verification_id = $val AND code_active = 1";
      $VERCODEEXQ = mysqli_query($con, $VERCODEEX);
      if (mysqli_num_rows($VERCODEEXQ) < 1) {
        // if we are unable to retrieve the data of the verification code then something must gone wrong //
        echo "<script>alert('WARNING: Unable to retrieve the data. Please try again.');";
        echo "</script>";
      } else {
        while ($row = mysqli_fetch_array($VERCODEEXQ)) {
          // these variables will be used in the latter part //
          $userid = $row["user_id_code"];
        }
        // if the verification code existing in the database then we will start to disable the code //
        $DISABLEVER = "UPDATE verification_code SET code_active = 0 WHERE verification_id = ?";
        // then we will start to update the information //
        $stmt = $con->prepare($DISABLEVER);
        $stmt->bind_param("i", $val);
        $stmt->execute();

        if (($stmt->error) == FALSE) {
          // if there is no error then close the previous statement //
          $stmt->close();
          // then according to the concept of our system, anyone which is under the verification code that is not active will automatically disabled //
          $suspended_reason = "Account Disabled due to Verification Code being disabled by an Admin, ID: ".$_SESSION['user_id'].". For more information please do contact with the Administrator.";
          $DISABLEUSER = "UPDATE user SET active = 0, suspended_reason = '".$suspended_reason."' WHERE user_id = ?";
          // then we will start to update the information //
          $stmt = $con->prepare($DISABLEUSER);
          $stmt->bind_param("i", $userid);
          $stmt->execute();

          if (($stmt->error) == FALSE) {
            // if there is no error then close the previous statement //
            $stmt->close();
            // if we have reached this point then the disable process is a success //
            echo "<script>alert('Notice: Disable Successful for Verification ID: ".$val.".');";
            echo "</script>";
          } else {
            // if we are unable to disable the verification code then something must gone wrong //
            echo "<script>alert('WARNING: Unable to disable the related User Account ID: ".$userid.". Please try again. Possible Error: ".mysqli_error($con)."');";
            echo "</script>";
          }
        } else {
          // if we are unable to disable the verification code then something must gone wrong //
          echo "<script>alert('WARNING: Unable to disable the Verification Code. Please try again. Possible Error: ".mysqli_error($con)."');";
          echo "</script>";
        }
      }
    }
    // if the process is entirely over then alert the admin //
    echo "<script>alert('Notice: All disable process is now complete. Now returning to Admin Panel.');";
    echo "window.location.href='admin_panel.html';</script>";
  }
  //***END OF PROCESS***//
}
// End of the function: Verification Code's Verification Form: Disable //
?>

<?php
// Begin of the function: Verification Code's Verification Form: Reset //
// First we check the form has submitted or not //
if (isset($_POST['submit-list-resA'])) {
  // If it is, then we will retreive data from the input forms //
  $verid = $_POST["verid"];
  $res_acccode = mysqli_real_escape_string($con, $_POST['res-acccode']);
  $res_pw = mysqli_real_escape_string($con, $_POST['res-pw']);
  // Taking the current time //
  date_default_timezone_set("Etc/GMT-8");
  $now = date("Y-m-d H:i:s");
  // Variable to store Error Message //
  $error = '';

  // Sorting out the query related to the function //
  // Verify the user is an admin or not //
  $VERFYADMIN = "SELECT * FROM user
                 WHERE status = 2 AND active = 1 AND account_code = '".md5($res_acccode)."' AND password = '".md5($res_pw)."'";
  $VERFYADMINQ = mysqli_query($con, $VERFYADMIN);

  //***BEGIN OF PROCESS***//
  if (mysqli_num_rows($VERFYADMINQ) < 1) {
    // if the admin is not verified, then inform the user and send him back to admin panel //
    echo "<script>alert('ALERT: Information unable to be verified. Please try again.');";
    echo "window.location.href='admin_panel.html';</script>";
    exit(0);
  } else {
    // begin the process of resetting the verification code //
    // P.S: Resetting the Verification Code means reset the code back to "factory version", where the user account under the code will be disabled and the verification code itself will be not under any user account //
    while (list($key,$val) = @each ($verid)) {
      // first we check the verification code exists in database or not //
      $VERCODEEX = "SELECT * FROM verification_code
                    WHERE verification_id = $val";
      $VERCODEEXQ = mysqli_query($con, $VERCODEEX);
      if (mysqli_num_rows($VERCODEEXQ) < 1) {
        // if we are unable to retrieve the data of the verification code then something must gone wrong //
        echo "<script>alert('WARNING: Unable to retrieve the data. Please try again.');";
        echo "</script>";
      } else {
        while ($row = mysqli_fetch_array($VERCODEEXQ)) {
          // these variables will be used in the latter part //
          if (isset($row["user_id_code"])) {
            $userid = $row["user_id_code"];
          }
        }
        // if the verification code existing in the database then we will start to resetting the code //
        $RESETVER = "UPDATE verification_code SET code_active = 1, code_used = 0, user_id_code = NULL WHERE verification_id = ?";
        // then we will start to update the information //
        $stmt = $con->prepare($RESETVER);
        $stmt->bind_param("i", $val);
        $stmt->execute();

        if (($stmt->error) == FALSE) {
          // if there is no error then close the previous statement //
          $stmt->close();
          // then if the verification_code is under some user account then we will disable that user //
          if (isset($userid)) {
            // then we will start to disable the user that was under the verification code //
            $suspended_reason = "Account Disabled due to Verification Code being reset by an Admin, ID: ".$_SESSION['user_id'].". For more information please do contact with the Administrator.";
            $DISABLEUSER = "UPDATE user SET active = 0, suspended_reason = '".$suspended_reason."', verification_code = NULL WHERE user_id = ?";
            // then we will start to update the information //
            $stmt = $con->prepare($DISABLEUSER);
            $stmt->bind_param("i", $userid);
            $stmt->execute();

            if (($stmt->error) == FALSE) {
              // if there is no error then close the previous statement //
              $stmt->close();
            } else {
              // if we are unable to disable the verification code then something must gone wrong //
              echo "<script>alert('WARNING: Unable to disable the related User Account ID: ".$userid.". Please try again. Possible Error: ".mysqli_error($con)."');";
              echo "</script>";
            }
          }
          // if we have reached this point then the reset process is a success //
          echo "<script>alert('Notice: Reset Successful for Verification ID: ".$val.".');";
          echo "</script>";
        } else {
          // if we are unable to disable the verification code then something must gone wrong //
          echo "<script>alert('WARNING: Unable to disable the Verification Code. Please try again. Possible Error: ".mysqli_error($con)."');";
          echo "</script>";
        }
      }
    }
    // if the process is entirely over then alert the admin //
    echo "<script>alert('Notice: All reset process is now complete. Now returning to Admin Panel.');";
    echo "window.location.href='admin_panel.html';</script>";
  }
  //***END OF PROCESS***//
}
// End of the function: Verification Code's Verification Form: Reset //
?>

<?php
// Begin of the function: Verification Code's Verification Form: Delete //
// First we check the form has submitted or not //
if (isset($_POST['submit-list-delA'])) {
  // If it is, then we will retreive data from the input forms //
  $verid = $_POST["verid"];
  $del_acccode = mysqli_real_escape_string($con, $_POST['del-acccode']);
  $del_pw = mysqli_real_escape_string($con, $_POST['del-pw']);
  // Taking the current time //
  date_default_timezone_set("Etc/GMT-8");
  $now = date("Y-m-d H:i:s");
  // Variable to store Error Message //
  $error = '';

  // Sorting out the query related to the function //
  // Verify the user is an admin or not //
  $VERFYADMIN = "SELECT * FROM user
                 WHERE status = 2 AND active = 1 AND account_code = '".md5($del_acccode)."' AND password = '".md5($del_pw)."'";
  $VERFYADMINQ = mysqli_query($con, $VERFYADMIN);

  //***BEGIN OF PROCESS***//
  if (mysqli_num_rows($VERFYADMINQ) < 1) {
    // if the admin is not verified, then inform the user and send him back to admin panel //
    echo "<script>alert('ALERT: Information unable to be verified. Please try again.');";
    echo "window.location.href='admin_panel.html';</script>";
    exit(0);
  } else {
    // begin the process of resetting the verification code //
    // P.S: Resetting the Verification Code means reset the code back to "factory version", where the user account under the code will be disabled and the verification code itself will be not under any user account //
    while (list($key,$val) = @each ($verid)) {
      // first we check the verification code exists in database or not //
      $VERCODEEX = "SELECT * FROM verification_code
                    WHERE verification_id = $val";
      $VERCODEEXQ = mysqli_query($con, $VERCODEEX);
      if (mysqli_num_rows($VERCODEEXQ) < 1) {
        // if we are unable to retrieve the data of the verification code then something must gone wrong //
        echo "<script>alert('WARNING: Unable to retrieve the data. Please try again.');";
        echo "</script>";
      } else {
        while ($row = mysqli_fetch_array($VERCODEEXQ)) {
          // these variables will be used in the latter part //
          if (isset($row["user_id_code"])) {
            $userid = $row["user_id_code"];
          }
        }
        // if the verification code existing in the database then we will start to delete the code //
        // But first we have to update the related Foreign Keys: registered_admin_id AND user_id_code to NULL due to SYNTAX in phpMyAdmin that doesn't allow deletion if there's a connected foreign key //
        $NULLVER = "UPDATE verification_code SET registered_admin_id = NULL, user_id_code = NULL WHERE verification_id = ?";
        // then we will start to update the information //
        $stmt = $con->prepare($NULLVER);
        $stmt->bind_param("i", $val);
        $stmt->execute();

        if (($stmt->error) == FALSE) {
          // if there is no error then close the previous statement //
          $stmt->close();
          // if there is a value inside the $userid then this means there is a user under the verification code. We will make the Foreign Key within the table which related to verification_code table to become NULL //
          if (isset($userid)) {
            // we go to the user table make the Foreign Key: verification_code which related with the verification_code table to NULL so we only can start the deletion process, but depends on whether the user_id_code originally got a value or not //
            $NULLUSE = "UPDATE user SET verification_code = NULL WHERE user_id = ?";
            // then we will start to update the information //
            $stmt = $con->prepare($NULLUSE);
            $stmt->bind_param("i", $userid);
            $stmt->execute();
            if (($stmt->error) == FALSE) {
              // if there is no error then close the previous statement //
              $stmt->close();
            } else {
              // if we are unable to update the user's info then something must gone wrong //
              echo "<script>alert('WARNING: Unable to update the User_ID: ".$userid.". Please try again. Possible Error: ".mysqli_error($con)."');";
              echo "</script>";
              $stmt->close();
              exit(0);
            }
          }
            // if the Foreign Keys were updated then proceed the deletion process //
            $DELETEVER = "DELETE FROM verification_code WHERE verification_id = ?";
            // then we will start to delete the code //
            $stmt = $con->prepare($DELETEVER);
            $stmt->bind_param("i", $val);
            $stmt->execute();

            if (($stmt->error) == FALSE) {
              // if there is no error then close the previous statement //
              $stmt->close();
              // if there is a value inside the $userid then this means there is a user under the verification code. We will disable that user account based on the concept of our system //
              if (isset($userid)) {
                // then we will start to disable the user that was under the verification code //
                $suspended_reason = "Account Disabled due to Verification Code being deleted by an Admin, ID: ".$_SESSION['user_id'].". For more information please do contact with the Administrator.";
                $DISABLEUSER = "UPDATE user SET active = 0, suspended_reason = '".$suspended_reason."', verification_code = NULL WHERE user_id = ?";
                // then we will start to update the information //
                $stmt = $con->prepare($DISABLEUSER);
                $stmt->bind_param("i", $userid);
                $stmt->execute();
                if (($stmt->error) == FALSE) {
                  // if there is no error then close the previous statement //
                  $stmt->close();
                } else {
                  // if we are unable to disable the verification code then something must gone wrong //
                  echo "<script>alert('WARNING: Unable to disable the related User Account ID: ".$userid.". Please try again. Possible Error: ".mysqli_error($con)."');";
                  echo "</script>";
                  $stmt->close();
                  exit(0);
                }
              }
              // if we have reached this point then the reset process is a success //
              echo "<script>alert('Notice: Deletion Successful for Verification ID: ".$val.".');";
              echo "</script>";
            } else {
              // if we are unable to disable the verification code then something must gone wrong //
              echo "<script>alert('WARNING: Unable to delete the Verification Code. Please try again. Possible Error: ".mysqli_error($con)."');";
              echo "</script>";
            }
        } else {
          // if we are unable to update the verification code's info then something must gone wrong //
          echo "<script>alert('WARNING: Unable to update the Verification_ID: ".$val.". Please try again. Possible Error: ".mysqli_error($con)."');";
          echo "</script>";
        }
      }
    }
    // if the process is entirely over then alert the admin //
    echo "<script>alert('Notice: All deletion process is now complete. Now returning to Admin Panel.');";
    echo "window.location.href='admin_panel.html';</script>";
  }
  //***END OF PROCESS***//
}
// End of the function: Verification Code's Verification Form: Delete //
?>

<?php
// Begin of the function: Verification Code's Verification Form: Registration //
// First we check the form has submitted or not //
if (isset($_POST['submit-list-regA'])) {
  // If it is, then we will retreive data from the input forms //
  $reg_acccode = mysqli_real_escape_string($con, $_POST['reg-acccode']);
  $reg_pw = mysqli_real_escape_string($con, $_POST['reg-pw']);
  $reg_role = mysqli_real_escape_string($con, $_POST['vercode-role']);
  $reg_vercode = mysqli_real_escape_string($con, $_POST['vercode-new']);

  // Taking the current time //
  date_default_timezone_set("Etc/GMT-8");
  $now = date("Y-m-d H:i:s");
  // Variable to store Error Message //
  $error = '';

  // Sorting out the query related to the function //
  // Verify the user is an admin or not //
  $VERFYADMIN = "SELECT * FROM user
                 WHERE status = 2 AND active = 1 AND account_code = '".md5($reg_acccode)."' AND password = '".md5($reg_pw)."'";
  $VERFYADMINQ = mysqli_query($con, $VERFYADMIN);

  //***BEGIN OF PROCESS***//
  if (mysqli_num_rows($VERFYADMINQ) < 1) {
    // if the admin is not verified, then inform the user and send him back to admin panel //
    echo "<script>alert('ALERT: Information unable to be verified. Please try again.');";
    echo "window.location.href='admin_panel.html';</script>";
    exit(0);
  } else {
    // begin the process of registering the verification code //
    $adminid = $_SESSION["user_id"];
    $code_active = 1;
    $code_used = 0;
    $REGVER = "INSERT INTO verification_code (verification_code, code_active, code_used, code_status, registered_date, registered_admin_id)
               VALUES (?,?,?,?,?,?)";
    $stmt = $con->prepare($REGVER);
    $stmt->bind_param("sssssi", $reg_vercode, $code_active, $code_used, $reg_role, $now, $adminid);
    $stmt->execute();

    if (($stmt->error) == FALSE) {
      // if there is no error then close the previous statement //
      $stmt->close();
      // if we have reached this point then the registration is a success //
      echo "<script>alert('Notice: New verification code registered.');";
      echo "</script>";
    } else {
      // if we are unable to insert the new verification code then something must gone wrong //
      echo "<script>alert('WARNING: Unable to register the new verification code. Please try again. Possible Error: ".mysqli_error($con)."');";
      echo "</script>";
      exit(0);
    }
    // if the process is entirely over then alert the admin //
    echo "<script>alert('Notice: All registration process of the verification code is now complete. Now returning to Admin Panel.');";
    echo "window.location.href='admin_panel.html';</script>";
  }
  //***END OF PROCESS***//
}
// End of the function: Verification Code's Verification Form: Registration //
?>

<?php
// Begin of the function: Verification Code's Verification Form: Enable //
// First we check the form has submitted or not //
if (isset($_POST['submit-list-enableS'])) {
  // If it is, then we will retreive data from the input forms //
  $verid = $_POST["verid"];
  $enable_acccode = mysqli_real_escape_string($con, $_POST['enable-acccode']);
  $enable_pw = mysqli_real_escape_string($con, $_POST['enable-pw']);
  // Taking the current time //
  date_default_timezone_set("Etc/GMT-8");
  $now = date("Y-m-d H:i:s");
  // Variable to store Error Message //
  $error = '';

  // Sorting out the query related to the function //
  // Verify the user is an admin or not //
  $VERFYADMIN = "SELECT * FROM user
                 WHERE status = 2 AND active = 1 AND account_code = '".md5($enable_acccode)."' AND password = '".md5($enable_pw)."'";
  $VERFYADMINQ = mysqli_query($con, $VERFYADMIN);

  //***BEGIN OF PROCESS***//
  if (mysqli_num_rows($VERFYADMINQ) < 1) {
    // if the admin is not verified, then inform the user and send him back to admin panel //
    echo "<script>alert('ALERT: Information unable to be verified. Please try again.');";
    echo "window.location.href='admin_panel.html';</script>";
    exit(0);
  } else {
    // begin the process of disabling the verification code //
    while (list($key,$val) = @each ($verid)) {
      // first we check the verification code exists in database or not //
      $VERCODEEX = "SELECT * FROM verification_code
                    WHERE verification_id = $val";
      $VERCODEEXQ = mysqli_query($con, $VERCODEEX);
      if (mysqli_num_rows($VERCODEEXQ) < 1) {
        // if we are unable to retrieve the data of the verification code then something must gone wrong //
        echo "<script>alert('WARNING: Unable to retrieve the data. Please try again.');";
        echo "</script>";
      } else {
        while ($row = mysqli_fetch_array($VERCODEEXQ)) {
          $code_active = $row["code_active"];
          // these variables will be used in the latter part //
          if (isset($row["user_id_code"])) {
            $userid = $row["user_id_code"];
          }
        }
        // before entering the process we first check the verification code is truly inactive or not //
        if ($code_active > 0) {
          // if the code is already active then tell the user this ID is already active //
          echo "<script>alert('Notice: Verification_ID: ".$val." already active.');";
          echo "</script>";
        } else {
          // if its not then we proceed the process //
          // if the verification code existing in the database then we will start to enable the code //
          $ENABLEVER = "UPDATE verification_code SET code_active = 1 WHERE verification_id = ?";
          // then we will start to update the information //
          $stmt = $con->prepare($ENABLEVER);
          $stmt->bind_param("i", $val);
          $stmt->execute();

          if (($stmt->error) == FALSE) {
            // if there is no error then close the previous statement //
            $stmt->close();
            // if the vercode has been reactivated then we have to check and enable the user under the vercode as well //
            if (isset($userid)) {
              $ENABLEUSER = "UPDATE user SET active = 1 WHERE user_id = ?";
              // then we will start to update the information //
              $stmt = $con->prepare($ENABLEUSER);
              $stmt->bind_param("i", $userid);
              $stmt->execute();

              if (($stmt->error) == FALSE) {
                // if there is no error then close the previous statement //
                echo "<script>alert('Notice: User_ID: ".$userid." which under Verification ID: ".$val." is reactivated.');";
                echo "</script>";
                $stmt->close();
              } else {
                // if we are unable to disable the verification code then something must gone wrong //
                echo "<script>alert('WARNING: Unable to reactivate the related User Account ID: ".$userid.". Please try again. Possible Error: ".mysqli_error($con)."');";
                echo "</script>";
              }
            }
            // if we have reached this point then the enable process is a success //
            echo "<script>alert('Notice: Enable Successful for Verification ID: ".$val.".');";
            echo "</script>";
          } else {
            // if we are unable to disable the verification code then something must gone wrong //
            echo "<script>alert('WARNING: Unable to enable the Verification Code. Please try again. Possible Error: ".mysqli_error($con)."');";
            echo "</script>";
          }
        }
      }
    }
    // if the process is entirely over then alert the admin //
    echo "<script>alert('Notice: All enable process is now complete. Now returning to Admin Panel.');";
    echo "window.location.href='admin_panel.html';</script>";
  }
  //***END OF PROCESS***//
}
// End of the function: Verification Code's Verification Form: Enable //
?>
