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
// Begin of the function: Registration List's Verification Form: Registration //
// First we check the form has submitted or not //
if (isset($_POST['submit-list-reg'])) {
  // If it is, then we will retreive data from the input forms //
  $regid = $_POST["regid"];
  $reg_acccode = mysqli_real_escape_string($con, $_POST['reg-acccode']);
  $reg_pw = mysqli_real_escape_string($con, $_POST['reg-pw']);
  // Taking the current time //
  date_default_timezone_set("Etc/GMT-8");
  $now = date("Y-m-d H:i:s");
  // Variable to store Error Message //
  $error = '';
  // Alphanumeric Generator //
  function random_strings($length_of_string) {
    // String of all alphanumeric character
    $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    // Shufle the $str_result and returns substring
    // of specified length
    return substr(str_shuffle($str_result), 0, $length_of_string);
  }

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
    // begin the process of registration //
    while (list($key,$val) = @each ($regid)) {
      // Now to verify the user's legitimacy //
      // Take the user's vercode into variable first //
      $USERVERCODE = "SELECT * FROM registration_list
                      WHERE registration_id = $val AND verified = 0";
      $USERVERCODEQ = mysqli_query($con, $USERVERCODE);
      if (mysqli_num_rows($USERVERCODEQ) < 1) {
        // if we are unable to retrieve the data of the registering user then something must gone wrong //
        echo "<script>alert('WARNING: Unable to retrieve the data. Please try again.');";
        echo "</script>";
      } else {
        while ($row = mysqli_fetch_array($USERVERCODEQ)) {
          $vercode = $row["verification_code"];
          // these variables will be used in the latter part //
          $fname = $row["first_name"];
          $lname = $row["last_name"];
          $dob = $row["dob"];
          $gender = $row["gender"];
        }
          // since we got the value of the vercode then we start to define the query //
          $VERCODE = "SELECT * FROM verification_code WHERE verification_code = '".$vercode."' AND code_active = 1";
          $VERCODEQ = mysqli_query($con, $VERCODE);
          if (mysqli_num_rows($VERCODEQ) < 1) {
            // if we are unable to retrieve the data of the registering user then something must gone wrong //
            echo "<script>alert('WARNING: Unable to retrieve the info of VERCODE. Please try again.');";
            echo "</script>";
          } else {
            while ($row = mysqli_fetch_array($VERCODEQ)) {
              $status = $row["code_status"];
            }
              // we will first insert the user main information into the database: i.e. password, username, etc. //
              $account_code = random_strings(8);
              $APPROVE = "INSERT INTO user (username, password, email, account_id, account_code, decrypted_account_code, active, status, registered_date, verification_code)
                          SELECT username, password, email, account_id, '".md5($account_code)."', '".$account_code."', 1, ?, ?, verification_code
                          FROM registration_list
                          WHERE registration_id = ?";
              $stmt = $con->prepare($APPROVE);
              $stmt->bind_param("ssi", $status, $now, $val);
              $stmt->execute();
            if (($stmt->error) == FALSE) {
              // if there is no error then close the previous statement //
              $stmt->close();
              $adminid = $_SESSION['user_id'];
              // now we define the query based on the role of the registering user: 0 = student, 1 = cashier, 2 = admin //
              if ($status == 0) {
                // Student table //
                $accesscode = random_strings(8);
                $USERINFO = "INSERT INTO student (first_name, last_name, gender, dob, profile_image, access_code, decrypted_access_code, admin_approved, balance)
                             SELECT first_name, last_name, gender, dob, image_profile, '".md5($accesscode)."', '".$accesscode."', $adminid, 0.00
                             FROM registration_list
                             WHERE registration_id = ?";
              } else if ($status == 1) {
                // Cashier table //
                $USERINFO = "INSERT INTO cashier (first_name, last_name, gender, dob, profile_image, admin_approved)
                             SELECT first_name, last_name, gender, dob, image_profile, $adminid
                             FROM registration_list
                             WHERE registration_id = ?";
              } else if ($status == 2) {
                // Admin table //
                $USERINFO = "INSERT INTO admin (first_name, last_name, gender, profile_image, admin_approved)
                             SELECT first_name, last_name, gender, image_profile, $adminid
                             FROM registration_list
                             WHERE registration_id = ?";
              }
              // then we will start to insert the information //
              $stmt = $con->prepare($USERINFO);
              $stmt->bind_param("i", $val);
              $stmt->execute();

              if (($stmt->error) == FALSE) {
                // if there is no error then close the previous statement //
                $stmt->close();
                // then we will now update the registration list that the registration has been submitted //
                $UPDATEREGLIST = "UPDATE registration_list SET verified = 1 WHERE registration_id = ?";
                // then we will start to update the information //
                $stmt = $con->prepare($UPDATEREGLIST);
                $stmt->bind_param("i", $val);
                $stmt->execute();

                if (($stmt->error) == FALSE) {
                  // if there is no error then close the previous statement //
                  $stmt->close();
                  // now we will have to update the last two part of the database by linking the newly registered user_id into two important tables: role table and verification_code table //
                  // first we define the query which search for the user_id and take into variable //
                  $USERID = "SELECT user_id FROM user
                             WHERE status = $status AND active = 1 AND verification_code = '".$vercode."'";
                  $USERIDQ = mysqli_query($con, $USERID);
                  if (mysqli_num_rows($USERIDQ) < 1) {
                    // if we are unable to retrieve the user ID of the registering user then something must gone wrong //
                    echo "<script>alert('WARNING: Unable to retrieve the User ID. Please try again. Possible Error: ".mysqli_error($con)."');";
                    echo "</script>";
                  } else {
                    while ($row = mysqli_fetch_array($USERIDQ)) {
                      $userid = $row["user_id"];
                    }
                    // since we got the user_id then we will update the verification code and the related role table now //
                    $UPDATEVERCODE = "UPDATE verification_code SET user_id_code = ? WHERE verification_code = ? AND code_active = 1";
                    // we will start to update the information of verification code //
                    $stmt = $con->prepare($UPDATEVERCODE);
                    $stmt->bind_param("is", $userid, $vercode);
                    $stmt->execute();
                    if (($stmt->error) == FALSE) {
                      // if there is no error then close the previous statement //
                      $stmt->close();
                      // then finally, we will update the last part, which is the role related table //
                      // now we define the query based on the role of the registered user: 0 = student, 1 = cashier, 2 = admin //
                      if ($status == 0) {
                        // Student table //
                        $UPDATEID = "UPDATE student SET user_id = ? WHERE admin_approved = ? AND first_name = ? AND last_name = ? AND dob = '".$dob."' AND gender = ?";
                      } else if ($status == 1) {
                        // Cashier table //
                        $UPDATEID = "UPDATE cashier SET user_id = ? WHERE admin_approved = ? AND first_name = ? AND last_name = ? AND dob = '".$dob."' AND gender = ?";
                      } else if ($status == 2) {
                        // Admin table //
                        $UPDATEID = "UPDATE admin SET user_id = ? WHERE admin_approved = ? AND first_name = ? AND last_name = ? AND gender = ?";
                      }
                      // we will start the final update of the information //
                      $stmt = $con->prepare($UPDATEID);
                      $stmt->bind_param("iissi", $userid, $adminid, $fname, $lname, $gender);
                      $stmt->execute();
                      if (($stmt->error) == FALSE) {
                        // if there is no error then close the previous statement //
                        $stmt->close();
                        // if we have reached this point this means the registration is a success //
                        echo "<script>alert('Notice: Registration Successful for Registration ID: ".$val.".');";
                        echo "</script>";
                      } else {
                        // if we are unable to update the information then something must gone wrong //
                        echo "<script>alert('WARNING: User ID unable to update to the role table. Possible Error: ".mysqli_error($con)."');";
                        echo "</script>";
                      }
                    } else {
                      // if we are unable to update the information then something must gone wrong //
                      echo "<script>alert('WARNING: User ID unable to update to VERCODE list. Possible Error: ".mysqli_error($con)."');";
                      echo "</script>";
                    }
                  }
                } else {
                  // if we are unable to update the registration list then something must gone wrong //
                  echo "<script>alert('WARNING: Registration List unable to be updated. Possible Error: ".mysqli_error($con)."');";
                  echo "</script>";
                }
              } else {
                // if we are unable to submit the user personal information then something must gone wrong //
                echo "<script>alert('WARNING: User data unable to register. Possible Error: ".mysqli_error($con)."');";
                echo "</script>";
              }
            } else {
              // if we are unable to submit the registration then something must gone wrong //
              echo "<script>alert('WARNING: Registration failed. Possible Error: ".mysqli_error($con)."');";
              echo "</script>";
            }
          }
        }
    }
    // if the process is entirely over then alert the admin //
    echo "<script>alert('Notice: All registration is now complete. Now returning to Admin Panel.');";
    echo "window.location.href='admin_panel.html';</script>";
  }
  //***END OF PROCESS***//
}
// End of the function: Registration List's Verification Form: Registration //
?>

<?php
// Begin of the function: Registration List's Verification Form: Removal //

// First we check the form has submitted or not //
if (isset($_POST['submit-list-del'])) {
  // If it is, then we will retreive data from the input forms //
  $regid = $_POST["regid"];
  $reg_acccode = mysqli_real_escape_string($con, $_POST['del-acccode']);
  $reg_pw = mysqli_real_escape_string($con, $_POST['del-pw']);
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
  } else {
    // begin the process of deletion //
    while (list($key,$val) = @each ($regid)) {
      // Take the user's vercode into variable first //
      $USERVERCODE = "SELECT * FROM registration_list
                      WHERE registration_id = $val AND verified = 0";
      $USERVERCODEQ = mysqli_query($con, $USERVERCODE);
      if (mysqli_num_rows($USERVERCODEQ) < 1) {
        // if we are unable to retrieve the data of the registering user then something must gone wrong //
        echo "<script>alert('WARNING: Unable to retrieve the data. Please try again.');";
        echo "</script>";
      } else {
        while ($row = mysqli_fetch_array($USERVERCODEQ)) {
          $vercode = $row["verification_code"];
        }
        // now define the deletion query //
        $DELETEREG = "DELETE FROM registration_list WHERE registration_id = ?";
        // then we will start to delete the registration //
        $stmt = $con->prepare($DELETEREG);
        $stmt->bind_param("i", $val);
        $stmt->execute();

        if (($stmt->error) == FALSE) {
          // if there is no error then close the previous statement //
          $stmt->close();
          // after the deletion of the registration list, we have to update back the vercode to the status of not being used yet //
          $UPDATEVERCODE = "UPDATE verification_code SET code_used = 0 WHERE verification_code = ?";
          // then we will start to update the status of verification code //
          $stmt = $con->prepare($UPDATEVERCODE);
          $stmt->bind_param("s", $vercode);
          $stmt->execute();

          if (($stmt->error) == FALSE) {
            // if there is no error then close the previous statement //
            $stmt->close();
            // if we have reached this point this means the deletion is a success //
            echo "<script>alert('Notice: Deletion Successful for Registration ID: ".$val.".');";
            echo "</script>";
          } else {
            // if we are unable to update the status of verification code then something must gone wrong //
            echo "<script>alert('WARNING: Verification Code: ".$vercode." unable to be updated. Possible Error: ".mysqli_error($con)."');";
            echo "</script>";
          }
        } else {
          // if we are unable to delete the registration list then something must gone wrong //
          echo "<script>alert('WARNING: Registration ID: ".$val." unable to be deleted. Possible Error: ".mysqli_error($con)."');";
          echo "</script>";
        }
      }
    }
    // if the process is entirely over then alert the admin //
    echo "<script>alert('Notice: All deletion is now complete. Now returning to Admin Panel.');";
    echo "window.location.href='admin_panel.html';</script>";
  }
  //***END OF PROCESS***//
}
// End of the function: Registration List's Verification Form: Removal //
?>
