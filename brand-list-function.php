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
// Begin of the function: Brand's Verification Form: Disable //
// First we check the form has submitted or not //
// P.S: Did not change any of the variable names because its unecessary //
if (isset($_POST['submit-list-dis-bran'])) {
  // If it is, then we will retreive data from the input forms //
  $branid = $_POST["branid"];
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
    // begin the process of disabling the brand //
    while (list($key,$val) = @each ($branid)) {
      // first we check the brand exists in database or not //
      $VERCODEEX = "SELECT * FROM meal_brand
                    WHERE brand_id = $val AND active = 1";
      $VERCODEEXQ = mysqli_query($con, $VERCODEEX);
      if (mysqli_num_rows($VERCODEEXQ) < 1) {
        // if we are unable to retrieve the data of the brand then something must gone wrong //
        echo "<script>alert('WARNING: Unable to retrieve the data, or the record is already disabled. Please try again.');";
        echo "</script>";
      } else {
        // if the brand existing in the database then we will start to disable the record //
        $DISABLEVER = "UPDATE meal_brand SET active = 0 WHERE brand_id = ?";
        // then we will start to update the information //
        $stmt = $con->prepare($DISABLEVER);
        $stmt->bind_param("i", $val);
        $stmt->execute();

        if (($stmt->error) == FALSE) {
          // if there is no error then close the previous statement //
          $stmt->close();
          // then according to the concept of our system, any meal which is under the brand that is not active will automatically disabled //
          $DISABLEUSER = "UPDATE meal SET active = 0 WHERE meal_brand_id = ?";
          // then we will start to update the information //
          $stmt = $con->prepare($DISABLEUSER);
          $stmt->bind_param("i", $val);
          $stmt->execute();

          if (($stmt->error) == FALSE) {
            // if there is no error then close the previous statement //
            $stmt->close();
            // if we have reached this point then the disable process is a success //
            echo "<script>alert('Notice: Disable Successful for Brand ID: ".$val.".');";
            echo "</script>";
          } else {
            // if we are unable to disable the brand then something must gone wrong //
            echo "<script>alert('WARNING: Unable to disable the related meal. Please try again. Possible Error: ".mysqli_error($con)."');";
            echo "</script>";
          }
        } else {
          // if we are unable to disable the brand then something must gone wrong //
          echo "<script>alert('WARNING: Unable to disable the brand. Please try again. Possible Error: ".mysqli_error($con)."');";
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
// Begin of the function: Brand's Verification Form: Delete //
// First we check the form has submitted or not //
if (isset($_POST['submit-list-del-bran'])) {
  // If it is, then we will retreive data from the input forms //
  $branid = $_POST["branid"];
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
    // begin the process of deleting the brand //
    while (list($key,$val) = @each ($branid)) {
      // first we check the brand exists in database or not //
      $VERCODEEX = "SELECT * FROM meal_brand
                    WHERE brand_id = $val";
      $VERCODEEXQ = mysqli_query($con, $VERCODEEX);
      if (mysqli_num_rows($VERCODEEXQ) < 1) {
        // if we are unable to retrieve the data of the brand then something must gone wrong //
        echo "<script>alert('WARNING: Unable to retrieve the data of the brand. Please try again.');";
        echo "</script>";
      } else {
        // if the brand existing in the database then we will start to delete the code //
        // But first we have to update the related Foreign Keys: admin_id to NULL due to SYNTAX in phpMyAdmin that doesn't allow deletion if there's a connected foreign key //
        $NULLVER = "UPDATE meal_brand SET admin_id = NULL WHERE brand_id = ?";
        // then we will start to update the information //
        $stmt = $con->prepare($NULLVER);
        $stmt->bind_param("i", $val);
        $stmt->execute();

        if (($stmt->error) == FALSE) {
          // if there is no error then close the previous statement //
          $stmt->close();
          // now we go to the meal table make the Foreign Key: meal_brand_id which related with the meal_brand table to NULL and disable them at the same time so we only can start the deletion process, and disable them at the same time since under our system's concept any meal thats under the related brand once deleted/disabled, will be disabled as well //
          $NULLUSE = "UPDATE meal SET meal_brand_id = NULL, active = 0 WHERE meal_brand_id = ?";
          // then we will start to update the information //
          $stmt = $con->prepare($NULLUSE);
          $stmt->bind_param("i", $val);
          $stmt->execute();
          if (($stmt->error) == FALSE) {
            // if there is no error then close the previous statement //
            $stmt->close();
            // the finally, set the final table that contain the Foreign Key: meal_brand_id from transaction_record table //
            // now we go to the meal table make the Foreign Key: meal_brand_id which related with the meal_brand table to NULL and disable them at the same time so we only can start the deletion process //
            $NULLRECORD = "UPDATE transaction_record SET meal_brand_id = NULL WHERE meal_brand_id = ?";
            // then we will start to update the information //
            $stmt = $con->prepare($NULLRECORD);
            $stmt->bind_param("i", $val);
            $stmt->execute();
            if (($stmt->error) == FALSE) {
              // if there is no error then close the previous statement //
              $stmt->close();
              // if the Foreign Keys were updated then proceed the deletion process //
              $DELETEVER = "DELETE FROM meal_brand WHERE brand_id = ?";
              // then we will start to delete the code //
              $stmt = $con->prepare($DELETEVER);
              $stmt->bind_param("i", $val);
              $stmt->execute();
              if (($stmt->error) == FALSE) {
                // if there is no error then close the previous statement //
                $stmt->close();
                // if we have reached this point then the reset process is a success //
                echo "<script>alert('Notice: Deletion Successful for Brand ID: ".$val.".');";
                echo "</script>";
              } else {
                // if we are unable to delete the brand then something must gone wrong //
                echo "<script>alert('WARNING: Unable to delete the brand. Please try again. Possible Error: ".mysqli_error($con)."');";
                echo "</script>";
              }
            } else {
              // if we are unable to update the user's info then something must gone wrong //
              echo "<script>alert('WARNING: Unable to update the related transaction record table. Please try again. Possible Error: ".mysqli_error($con)."');";
              echo "</script>";
            }
          } else {
            // if we are unable to update the user's info then something must gone wrong //
            echo "<script>alert('WARNING: Unable to update the related meals. Please try again. Possible Error: ".mysqli_error($con)."');";
            echo "</script>";
          }
        } else {
          // if we are unable to update the verification code's info then something must gone wrong //
          echo "<script>alert('WARNING: Unable to update the Brand_ID: ".$val.". Please try again. Possible Error: ".mysqli_error($con)."');";
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
// End of the function: Brand's Verification Form: Delete //
?>

<?php
// Begin of the function: Brand's Verification Form: Registration //
// First we check the form has submitted or not //
if (isset($_POST['submit-list-reg-bran'])) {
  // If it is, then we will retreive data from the input forms //
  $reg_acccode = mysqli_real_escape_string($con, $_POST['reg-acccode']);
  $reg_pw = mysqli_real_escape_string($con, $_POST['reg-pw']);
  $reg_bran_name = mysqli_real_escape_string($con, $_POST['reg-bran-name']);
  // defining the input of the image //
  if(file_exists($_FILES['bran-img']['tmp_name']) || is_uploaded_file($_FILES['bran-img']['tmp_name'])) {
     // Define Photo Uploads' path and variables //
    $target_dir = "/APU/SDP/image/";
    $target_file = $target_dir . basename($_FILES["bran-img"]["name"]);
    $imageFileType = pathinfo ($target_file, PATHINFO_EXTENSION);
    //check if image file is a actual image or fake image
    $check = getimagesize($_FILES["bran-img"]["tmp_name"]);
    if($check !== false)  {
      echo "<script>alert('File is an image - " . $check["mime"] . ".');</script>";
    } else {
      echo "<script>alert('File is not an image.Please try again!');</script>";
      echo "window.location.href='admin_panel.html';</script>";
    }
    //Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
      echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.Please try again!');</script>";
      echo "window.location.href='admin_panel.html';</script>";
    }
  } else {
    $target_file = NULL;
  }
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
    // begin the process of registering the brand //
    $adminid = $_SESSION["user_id"];
    $code_active = 1;
    $code_used = 0;
    $REGBRAN = "INSERT INTO meal_brand (brand_image, brand_name, registered_date, admin_id, active)
                VALUES (?,?,?,?,?)";
    $stmt = $con->prepare($REGBRAN);
    $stmt->bind_param("sssis", $target_file, $reg_bran_name, $now, $adminid, $code_active);
    $stmt->execute();

    if (($stmt->error) == FALSE) {
      // if there is no error then close the previous statement //
      $stmt->close();
      // if we have reached this point then the registration is a success //
      echo "<script>alert('Notice: New brand registered.');";
      echo "</script>";
    } else {
      // if we are unable to insert the new brand then something must gone wrong //
      echo "<script>alert('WARNING: Unable to register the new brand. Please try again. Possible Error: ".mysqli_error($con)."');";
      echo "</script>";
      exit(0);
    }
    // if the process is entirely over then alert the admin //
    echo "<script>alert('Notice: All registration process of the brand is now complete. Now returning to Admin Panel.');";
    echo "window.location.href='admin_panel.html';</script>";
  }
  //***END OF PROCESS***//
}
// End of the function: Verification Code's Verification Form: Registration //
?>

<?php
// Begin of the function: Brand's Verification Form: Enable //
// First we check the form has submitted or not //
if (isset($_POST['submit-list-enable-bran'])) {
  // If it is, then we will retreive data from the input forms //
  $branid = $_POST["branid"];
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
    // begin the process of enabling the brand //
    while (list($key,$val) = @each ($branid)) {
      // first we check the verification code exists in database or not //
      $VERCODEEX = "SELECT * FROM meal_brand
                    WHERE brand_id = $val";
      $VERCODEEXQ = mysqli_query($con, $VERCODEEX);
      if (mysqli_num_rows($VERCODEEXQ) < 1) {
        // if we are unable to retrieve the data of the brand then something must gone wrong //
        echo "<script>alert('WARNING: Unable to retrieve the data of the brand. Please try again.');";
        echo "</script>";
      } else {
        while ($row = mysqli_fetch_array($VERCODEEXQ)) {
          $brand_active = $row["active"];
        }
        // before entering the process we first check the brand is truly inactive or not //
        if ($brand_active > 0) {
          // if the code is already active then tell the user this ID is already active //
          echo "<script>alert('Notice: Brand_ID: ".$val." already active.');";
          echo "</script>";
        } else {
          // if its not then we proceed the process //
          // if the brand existing in the database then we will start to enable the code //
          $ENABLEVER = "UPDATE meal_brand SET active = 1 WHERE brand_id = ?";
          // then we will start to update the information //
          $stmt = $con->prepare($ENABLEVER);
          $stmt->bind_param("i", $val);
          $stmt->execute();

          if (($stmt->error) == FALSE) {
            // if there is no error then close the previous statement //
            $stmt->close();
            // now we start to enable back the meals under the related brand //
            $CHECKMEAL = "SELECT * FROM meal WHERE meal_brand_id = $val";
            $CHECKMEALQ = mysqli_query($con, $CHECKMEAL);
            if (mysqli_num_rows($CHECKMEALQ) < 1) {
              // if we are unable to retrieve the data of the brand then something must gone wrong, or there's no meal under the brand yet //
              echo "<script>alert('Notice: There's no meal under the related Brand ID: ".$val.".');";
              echo "</script>";
            } else {
              // if there is then we will start to enable the related meals back //
              $ENABLEMEAL = "UPDATE meal SET active = 1 WHERE meal_brand_id = ?";
              // then we will start to update the information //
              $stmt = $con->prepare($ENABLEMEAL);
              $stmt->bind_param("i", $val);
              $stmt->execute();
              if (($stmt->error) == FALSE) {
                // if there is no error then close the previous statement //
                $stmt->close();
                echo "<script>alert('Notice: Enable Successful for meals under Brand ID: ".$val.".');";
                echo "</script>";
              } else {
                // if we are unable to enable the meals then something must gone wrong //
                echo "<script>alert('WARNING: Unable to enable the meals under Brand ID: ".$val.". Please try again. Possible Error: ".mysqli_error($con)."');";
                echo "</script>";
              }
            }
            // if we have reached this point then the enable process is a success //
            echo "<script>alert('Notice: Enable Successful for Brand ID: ".$val.".');";
            echo "</script>";
          } else {
            // if we are unable to enable the brand then something must gone wrong //
            echo "<script>alert('WARNING: Unable to enable the Brand ID: ".$val.". Please try again. Possible Error: ".mysqli_error($con)."');";
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
