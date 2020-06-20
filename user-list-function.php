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
// User List: Cashier //
// Begin of the function: User List: Cashier's Verification Form: Disable //
// First we check the form has submitted or not //
if (isset($_POST['submit-list-dis-cas'])) {
  // If it is, then we will retreive data from the input forms //
  $userid = $_POST["userid"];
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
    // begin the process of registration //
    while (list($key,$val) = @each ($userid)) {
      // first we check the user exists in database or not //
      $CHECKCAS = "SELECT * FROM user
                   WHERE user_id = $val AND status = 1 AND active = 1";
      $CHECKCASQ = mysqli_query($con, $CHECKCAS);
      if (mysqli_num_rows($CHECKCASQ) < 1) {
        // if we are unable to retrieve the data of the user then something must gone wrong //
        echo "<script>alert('WARNING: Either the user is already deactivated or there is no related data existing inside the database. Please try again.');";
        echo "</script>";
      } else {
        // if the user is existing in the database then we will start to disable the user, and insert the reason of suspend to the user as well //
        $suspended_reason = "Account Disabled due to account being disabled by an Admin, ID: ".$_SESSION['user_id'].". For more information please do contact with the Administrator.";
        $DISABLECAS = "UPDATE user SET active = 0, suspended_reason = '".$suspended_reason."' WHERE user_id = ?";
        // then we will start to update the information //
        $stmt = $con->prepare($DISABLECAS);
        $stmt->bind_param("i", $val);
        $stmt->execute();

        if (($stmt->error) == FALSE) {
          // if there is no error then close the previous statement //
          $stmt->close();
          // if we have reached this point then the disable process is a success //
          echo "<script>alert('Notice: Disable Successful for User ID: ".$val.".');";
          echo "</script>";
        } else {
          // if we are unable to disable the User then something must gone wrong //
          echo "<script>alert('WARNING: Unable to disable the User. Please try again. Possible Error: ".mysqli_error($con)."');";
          echo "</script>";
        }
      }

    }
    // if the process is entirely over then alert the admin //
    echo "<script>alert('Notice: All deactivation process is now complete. Now returning to Admin Panel.');";
    echo "window.location.href='admin_panel.html';</script>";
  }
  //***END OF PROCESS***//
}
// End of the function: User List: Cashier's Verification Form: Disable //
?>

<?php
// Begin of the function: User List: Cashier's Verification Form: Enable //
// First we check the form has submitted or not //
if (isset($_POST['submit-list-ena-cas'])) {
  // If it is, then we will retreive data from the input forms //
  $userid = $_POST["userid"];
  $ena_acccode = mysqli_real_escape_string($con, $_POST['ena-acccode']);
  $ena_pw = mysqli_real_escape_string($con, $_POST['ena-pw']);
  // Taking the current time //
  date_default_timezone_set("Etc/GMT-8");
  $now = date("Y-m-d H:i:s");
  // Variable to store Error Message //
  $error = '';

  // Sorting out the query related to the function //
  // Verify the user is an admin or not //
  $VERFYADMIN = "SELECT * FROM user
                 WHERE status = 2 AND active = 1 AND account_code = '".md5($ena_acccode)."' AND password = '".md5($ena_pw)."'";
  $VERFYADMINQ = mysqli_query($con, $VERFYADMIN);

  //***BEGIN OF PROCESS***//
  if (mysqli_num_rows($VERFYADMINQ) < 1) {
    // if the admin is not verified, then inform the user and send him back to admin panel //
    echo "<script>alert('ALERT: Information unable to be verified. Please try again.');";
    echo "window.location.href='admin_panel.html';</script>";
    exit(0);
  } else {
    // begin the process of registration //
    while (list($key,$val) = @each ($userid)) {
      // first we check the user exists in database or not //
      $CHECKCAS = "SELECT * FROM user
                   WHERE user_id = $val AND status = 1 AND active = 0";
      $CHECKCASQ = mysqli_query($con, $CHECKCAS);
      if (mysqli_num_rows($CHECKCASQ) < 1) {
        // if we are unable to retrieve the data of the user then something must gone wrong //
        echo "<script>alert('WARNING: Either the user is already activated or there is no related data existing inside the database. Please try again.');";
        echo "</script>";
      } else {
        // if the user is existing in the database then we will start to enable the user //
        $ENABLECAS = "UPDATE user SET active = 1 WHERE user_id = ?";
        // then we will start to update the information //
        $stmt = $con->prepare($ENABLECAS);
        $stmt->bind_param("i", $val);
        $stmt->execute();

        if (($stmt->error) == FALSE) {
          // if there is no error then close the previous statement //
          $stmt->close();
          // if we have reached this point then the enable process is a success //
          echo "<script>alert('Notice: Enable Successful for User ID: ".$val.".');";
          echo "</script>";
        } else {
          // if we are unable to enable the user then something must gone wrong //
          echo "<script>alert('WARNING: Unable to enable the User. Please try again. Possible Error: ".mysqli_error($con)."');";
          echo "</script>";
        }
      }

    }
    // if the process is entirely over then alert the admin //
    echo "<script>alert('Notice: All activation process is now complete. Now returning to Admin Panel.');";
    echo "window.location.href='admin_panel.html';</script>";
  }
  //***END OF PROCESS***//
}
// End of the function: User List: Cashier's Verification Form: Enable //
?>


<?php
// User List: Student //
// Begin of the function: Student List: Student's Verification Form: Enable/Disable //
// First we check the form has submitted or not //
if (isset($_POST['submit-list-action-student'])) {
  // If it is, then we will retreive data from the input forms //
  $userid = $_POST["userid"];
  $dis_acccode = mysqli_real_escape_string($con, $_POST['dis-acccode']);
  $dis_pw = mysqli_real_escape_string($con, $_POST['dis-pw']);
  $stud_action = mysqli_real_escape_string($con, $_POST['stud-action']);
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
    // begin the process of registration //
    while (list($key,$val) = @each ($userid)) {
      // first we check the user exists in database or not //
      $CHECKCAS = "SELECT * FROM user
                   WHERE user_id = $val AND status = 0 AND active = '".$stud_action."'";
      $CHECKCASQ = mysqli_query($con, $CHECKCAS);
      if (mysqli_num_rows($CHECKCASQ) > 0) {
        // if we are unable to retrieve the data of the user then something must gone wrong //
        echo "<script>alert('WARNING: Either the user is already at the status that you wanted to set, or there is no related data existing inside the database. Please try again.');";
        echo "</script>";
      } else {
        // defining the specific query when enabling/disabling the student //
        if ($stud_action == 1) {
          $suspended_reason = NULL;
        } else {
          $suspended_reason = "Account Disabled due to account being disabled by an Admin, ID: ".$_SESSION['user_id'].". For more information please do contact with the Administrator.";
        }
        $DISABLECAS = "UPDATE user SET active = '".$stud_action."', suspended_reason = '".$suspended_reason."' WHERE user_id = ?";
        // then we will start to update the information //
        $stmt = $con->prepare($DISABLECAS);
        $stmt->bind_param("i", $val);
        $stmt->execute();

        if (($stmt->error) == FALSE) {
          // if there is no error then close the previous statement //
          $stmt->close();
          // if we have reached this point then the disable process is a success //
          echo "<script>alert('Notice: Update Successful for User ID: ".$val.".');";
          echo "</script>";
        } else {
          // if we are unable to disable the User then something must gone wrong //
          echo "<script>alert('WARNING: Unable to perform the action on the User. Please try again. Possible Error: ".mysqli_error($con)."');";
          echo "</script>";
        }
      }

    }
    // if the process is entirely over then alert the admin //
    echo "<script>alert('Notice: All the process is now complete. Now returning to Admin Panel.');";
    echo "window.location.href='admin_panel.html';</script>";
  }
  //***END OF PROCESS***//
}
// End of the function: User List: Student's Verification Form: Enable/Disable //
?>

<?php
// Begin of the function: User List: Student's Verification Form: Top Up //
// First we check the form has submitted or not //
if (isset($_POST['submit-list-topup-stud'])) {
  // If it is, then we will retreive data from the input forms //
  $userid = $_POST["userid"];
  $ena_acccode = mysqli_real_escape_string($con, $_POST['ena-acccode']);
  $ena_pw = mysqli_real_escape_string($con, $_POST['ena-pw']);
  $top_up = mysqli_real_escape_string($con, $_POST['stud-top-up']);

  // Taking the current time //
  date_default_timezone_set("Etc/GMT-8");
  $now = date("Y-m-d H:i:s");
  // Variable to store Error Message //
  $error = '';

  // Sorting out the query related to the function //
  // Verify the user is an admin or not //
  $VERFYADMIN = "SELECT * FROM user
                 WHERE status = 2 AND active = 1 AND account_code = '".md5($ena_acccode)."' AND password = '".md5($ena_pw)."'";
  $VERFYADMINQ = mysqli_query($con, $VERFYADMIN);

  //***BEGIN OF PROCESS***//
  if (mysqli_num_rows($VERFYADMINQ) < 1) {
    // if the admin is not verified, then inform the user and send him back to admin panel //
    echo "<script>alert('ALERT: Information unable to be verified. Please try again.');";
    echo "window.location.href='admin_panel.html';</script>";
    exit(0);
  } else {
    // begin the process of registration //
    while (list($key,$val) = @each ($userid)) {
      // first we check the user exists in database or not //
      $CHECKCAS = "SELECT *
                   FROM user INNER JOIN student
                   ON user.user_id = student.user_id
                   WHERE student.user_id = $val AND user.status = 0 AND user.active = 1";
      $CHECKCASQ = mysqli_query($con, $CHECKCAS);
      if (mysqli_num_rows($CHECKCASQ) < 1) {
        // if we are unable to retrieve the data of the user then something must gone wrong //
        echo "<script>alert('WARNING: Database can\'t find any related data existing inside the database. Please try again.');";
        echo "</script>";
      } else {
        // we take in the amount of balance first so we can count it in the latter process //
        if ($row = mysqli_fetch_array($CHECKCASQ)) {
          $stud_balance = $row['balance'];
        }
        // we will begin to top up the user's balance //
        $TOPUPSTUD = "UPDATE user INNER JOIN student
                      ON user.user_id = student.user_id
                      SET student.balance = (student.balance + $top_up)
                      WHERE student.user_id = ?";
        // then we will start to update the information //
        $stmt = $con->prepare($TOPUPSTUD);
        $stmt->bind_param("i", $val);
        $stmt->execute();
        if (($stmt->error) == FALSE) {
          // if there is no error then close the previous statement //
          $stmt->close();
          $stud_balance = $stud_balance + $top_up;
          $top_up = "+".$top_up;
          // then we will have to update the balance_record table to update these info so in the future it will be easier to trace back //
          $UPDATEREC = "INSERT INTO balance_record (update_amount, update_date, update_method, balance_amount, user_id)
                        VALUES('".$top_up."', '".$now."', 0, '".$stud_balance."', ?)";
          // then we will start to insert the information //
          $stmt = $con->prepare($UPDATEREC);
          $stmt->bind_param("i", $val);
          $stmt->execute();
          if (($stmt->error) == FALSE) {
            // if there is no error then close the previous statement //
            $stmt->close();
            // if we have reached this point then the enable process is a success //
            echo "<script>alert('Notice: Top up Successful for User ID: ".$val.".');";
            echo "</script>";
          } else {
            // if we are unable to insert the balance records then something must gone wrong //
            echo "<script>alert('WARNING: Unable to insert the balance records. Please try again. Possible Error: ".mysqli_error($con)."');";
            echo "</script>";
          }
        } else {
          // if we are unable to top up the user then something must gone wrong //
          echo "<script>alert('WARNING: Unable to top up the User ID: ".$val.". Please try again. Possible Error: ".mysqli_error($con)."');";
          echo "</script>";
        }
      }
    }
    // if the process is entirely over then alert the admin //
    echo "<script>alert('Notice: All top-up process is now complete. Now returning to Admin Panel.');";
    echo "window.location.href='admin_panel.html';</script>";
  }
  //***END OF PROCESS***//
}
// End of the function: User List: Student's Verification Form: Top Up //
?>
