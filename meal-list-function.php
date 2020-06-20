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
// Begin of the function: Meal's Verification Form: Enable/Disable //
// First we check the form has submitted or not //
// P.S: Did not change any of the variable names except submit's input name because its unecessary //
if (isset($_POST['submit-list-action-meal'])) {
  // If it is, then we will retreive data from the input forms //
  $mealid = $_POST["mealid"];
  $dis_acccode = mysqli_real_escape_string($con, $_POST['dis-acccode']);
  $dis_pw = mysqli_real_escape_string($con, $_POST['dis-pw']);
  $meal_action = $_POST["meal-action"];
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
    while (list($key,$val) = @each ($mealid)) {
      // first we check the brand exists in database or not //
      $VERCODEEX = "SELECT * FROM meal
                    WHERE meal_id = $val";
      $VERCODEEXQ = mysqli_query($con, $VERCODEEX);
      if (mysqli_num_rows($VERCODEEXQ) < 1) {
        // if we are unable to retrieve the data of the meal then something must gone wrong //
        echo "<script>alert('WARNING: Unable to retrieve the data of the meal, please try again.');";
        echo "</script>";
      } else {
        // if the meal existing in the database then we will start to enable/disable the record depends on the radio button, 0: Disable, 1: Enable //
        $DISABLEVER = "UPDATE meal SET active = $meal_action WHERE meal_id = ?";
        if ($meal_action == 0) {
          $action = "Disable";
        } else {
          $action = "Enable";
        }
        // then we will start to update the information //
        $stmt = $con->prepare($DISABLEVER);
        $stmt->bind_param("i", $val);
        $stmt->execute();

        if (($stmt->error) == FALSE) {
          // if there is no error then close the previous statement //
          $stmt->close();
          // if we have reached this point then the disable process is a success //
          echo "<script>alert('Notice: ".$action." Successful for Meal ID: ".$val.".');";
          echo "</script>";
        } else {
          // if we are unable to disable the brand then something must gone wrong //
          echo "<script>alert('WARNING: Unable to ".$action." the meal. Please try again. Possible Error: ".mysqli_error($con)."');";
          echo "</script>";
        }
      }
    }
    // if the process is entirely over then alert the admin //
    echo "<script>alert('Notice: All meal action process is now complete. Now returning to Admin Panel.');";
    echo "window.location.href='admin_panel.html';</script>";
  }
  //***END OF PROCESS***//
}
// End of the function: Meal's Verification Form: Enable/Disable //
?>

<?php
// Begin of the function: Meal's Verification Form: Delete //
// First we check the form has submitted or not //
if (isset($_POST['submit-list-del-meal'])) {
  // If it is, then we will retreive data from the input forms //
  $mealid = $_POST["mealid"];
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
    // begin the process of deleting the meal //
    while (list($key,$val) = @each ($mealid)) {
      // first we check the meal exists in database or not //
      $VERCODEEX = "SELECT * FROM meal
                    WHERE meal_id = $val";
      $VERCODEEXQ = mysqli_query($con, $VERCODEEX);
      if (mysqli_num_rows($VERCODEEXQ) < 1) {
        // if we are unable to retrieve the data of the meal then something must gone wrong //
        echo "<script>alert('WARNING: Unable to retrieve the data of the meal. Please try again.');";
        echo "</script>";
      } else {
        // if the meal existing in the database then we will start to delete the code //
        // But first we have to update the related Foreign Keys: admin_id and meal_brand_id to NULL due to SYNTAX in phpMyAdmin that doesn't allow deletion if there's a connected foreign key //
        $NULLVER = "UPDATE meal SET admin_id = NULL, meal_brand_id = NULL WHERE meal_id = ?";
        // then we will start to update the information //
        $stmt = $con->prepare($NULLVER);
        $stmt->bind_param("i", $val);
        $stmt->execute();

        if (($stmt->error) == FALSE) {
          // if there is no error then close the previous statement //
          $stmt->close();
          // now we go to the transaction_record table and make the Foreign Key: meal_id which related with the meal table to NULL //
          $NULLUSE = "UPDATE transaction_record SET meal_id = NULL WHERE meal_id = ?";
          // then we will start to update the information //
          $stmt = $con->prepare($NULLUSE);
          $stmt->bind_param("i", $val);
          $stmt->execute();
          if (($stmt->error) == FALSE) {
            // if there is no error then close the previous statement //
            $stmt->close();
            // if the Foreign Keys were updated then proceed the deletion process //
            $DELETEVER = "DELETE FROM meal WHERE meal_id = ?";
            // then we will start to delete the code //
            $stmt = $con->prepare($DELETEVER);
            $stmt->bind_param("i", $val);
            $stmt->execute();
            if (($stmt->error) == FALSE) {
              // if there is no error then close the previous statement //
              $stmt->close();
              // if we have reached this point then the deletion process is a success //
              echo "<script>alert('Notice: Deletion Successful for Meal ID: ".$val.".');";
              echo "</script>";
            } else {
              // if we are unable to delete the meal then something must gone wrong //
              echo "<script>alert('WARNING: Unable to delete the meal. Please try again. Possible Error: ".mysqli_error($con)."');";
              echo "</script>";
            }
          } else {
            // if we are unable to update the meal's info then something must gone wrong //
            echo "<script>alert('WARNING: Unable to update the related meals. Please try again. Possible Error: ".mysqli_error($con)."');";
            echo "</script>";
            $stmt->close();
            exit(0);
          }
        } else {
          // if we are unable to update the meal's info then something must gone wrong //
          echo "<script>alert('WARNING: Unable to update the Meal_ID: ".$val.". Please try again. Possible Error: ".mysqli_error($con)."');";
          echo "</script>";
        }
      }
    }
    // if the process is entirely over then alert the admin //
    echo "<script>alert('Notice: All deletion process is now complete. Now returning to Admin Panel.');";
    echo "window.location.href='admin_panel.html';</script>";
  //***END OF PROCESS***//
}
}
// End of the function: Meal's Verification Form: Delete //
?>

<?php
// Begin of the function: Meal's Verification Form: Registration //
// First we check the form has submitted or not //
if (isset($_POST['submit-list-reg-meal'])) {
  // If it is, then we will retreive data from the input forms //
  $reg_acccode = mysqli_real_escape_string($con, $_POST['reg-acccode']);
  $reg_pw = mysqli_real_escape_string($con, $_POST['reg-pw']);
  $reg_meal_brand = mysqli_real_escape_string($con, $_POST['meal-reg-brand']);
  $reg_meal_name = mysqli_real_escape_string($con, $_POST['meal-name-reg']);
  $reg_meal_quantity = mysqli_real_escape_string($con, $_POST['meal-quantity-reg']);
  $reg_meal_price = mysqli_real_escape_string($con, $_POST['meal-price-reg']);
  $reg_meal_details = mysqli_real_escape_string($con, $_POST['meal-details-reg']);

  // defining the input of the image //
  if(file_exists($_FILES['meal-img-reg']['tmp_name']) || is_uploaded_file($_FILES['meal-img-reg']['tmp_name'])) {
     // Define Photo Uploads' path and variables //
    $target_dir = "/APU/SDP/image/";
    $target_file = $target_dir . basename($_FILES["meal-img-reg"]["name"]);
    $imageFileType = pathinfo ($target_file, PATHINFO_EXTENSION);
    //check if image file is an actual image or fake image
    $check = getimagesize($_FILES["meal-img-reg"]["tmp_name"]);
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
    // begin the process of registering the meal //
    $adminid = $_SESSION["user_id"];
    $code_active = 1;
    $code_used = 0;
    $REGMEAL = "INSERT INTO meal (active, meal_brand_id, meal_name, meal_image, meal_details, meal_price, meal_quantity, meal_additional_quantity, meal_default_quantity, admin_id)
                VALUES (?,?,?,?,?,?,?,?,?,?)";
    $stmt = $con->prepare($REGMEAL);
    $stmt->bind_param("sisssssssi", $code_active, $reg_meal_brand, $reg_meal_name, $target_file, $reg_meal_details, $reg_meal_price, $reg_meal_quantity, $code_used, $reg_meal_quantity, $adminid);
    $stmt->execute();

    if (($stmt->error) == FALSE) {
      // if there is no error then close the previous statement //
      $stmt->close();
      // if we have reached this point then the registration is a success //
      echo "<script>alert('Notice: New meal registered.');";
      echo "</script>";
    } else {
      // if we are unable to insert the new brand then something must gone wrong //
      echo "<script>alert('WARNING: Unable to register the new meal. Please try again. Possible Error: ".mysqli_error($con)."');";
      echo "</script>";
      exit(0);
    }
    // if the process is entirely over then alert the admin //
    echo "<script>alert('Notice: All registration process of the meal is now complete. Now returning to Admin Panel.');";
    echo "window.location.href='admin_panel.html';</script>";
  }
  //***END OF PROCESS***//
}
// End of the function: Meal's Verification Form: Registration //
?>

<?php
// Begin of the function: Meal's Verification Form: Editing //
// First we check the form has submitted or not //
if (isset($_POST['submit-list-edit-meal'])) {
  // If it is, then we will retreive data from the input forms //
  // but before that we have to check the user choose to do what: edit all the info or only the meal's brand based on the radio button //
  $meal_action = $_POST["meal-edit"];
  // 0 = Edit Meal's Brand only; 1 = Edit Meal's Info //
  // defining the query based on the action chosen by the admin //
  if ($meal_action == 1) {
    // by only editing info we have to allow the admin to update certain stuff by inputting, those without inputs will remain unchanged //
    $enable_acccode = mysqli_real_escape_string($con, $_POST['enable-acccode']);
    $enable_pw = mysqli_real_escape_string($con, $_POST['enable-pw']);
    $meal_edit_brand = mysqli_real_escape_string($con, $_POST['meal-edit-info-brand']);
    $mealid = ($_POST["mealid"][0]);

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
      if(file_exists($_FILES['meal-img']['tmp_name']) || is_uploaded_file($_FILES['meal-img']['tmp_name'])) {
         // Define Photo Uploads' path and variables //
        $target_dir = "/APU/SDP/image/";
        $target_file = $target_dir . basename($_FILES["meal-img"]["name"]);
        $imageFileType = pathinfo ($target_file, PATHINFO_EXTENSION);
        //check if image file is a actual image or fake image
        $check = getimagesize($_FILES["meal-img"]["tmp_name"]);
        if($check !== false)  {
          echo "<script>alert('File is an image - " . $check["mime"] . ".');</script>";
        } else {
          echo "<script>alert('File is not an image.Please try again!');</script>";
          die("<script>window.history.go(-1);</script>");
        }
        //Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
          echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.Please try again!');</script>";
          die("<script>window.history.go(-1);</script>");
        }
      } else {
        $target_file = NULL;
      }
      $form_fields = array('meal-img', 'meal-name-edit', 'meal-quantity-edit', 'meal-edit-info-brand', 'meal-price-edit', 'meal-details-edit');
      $data_names = array('meal_image', 'meal_name', 'meal_default_quantity', 'meal_brand_id', 'meal_price', 'meal_details');
      $sql = "";
      if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
        if (file_exists($_FILES['meal-img']['tmp_name']) || is_uploaded_file($_FILES['meal-img']['tmp_name'])) {
          $_POST["meal-img"] = $target_file;
        } else {
          $_POST["meal-img"] = NULL;
        }
        foreach(array_combine($data_names, $form_fields) as $data_field => $field_name) {
          if ( ! empty($_POST[$field_name] ) ){
              $variables = "$data_field = '".$_POST[$field_name]."'";
              $sql = "UPDATE meal SET $variables WHERE meal_id = $mealid";
              $UPDATEMEAL = mysqli_query($con, $sql);
          }
        }
        if ($con->query($sql) === TRUE) {
          // if the update is a success then we return to admin panel //
          echo "<script>alert('Notice: Update Succeed, now returning to Admin Panel.');";
      		echo "window.location.href='admin_panel.html';</script>";
        } else {
          // if we are unable to update the meal then something must gone wrong //
          echo "<script>alert('WARNING: Unable to update the Meal ID: ".$mealid.". Please try again. Possible Error: ".mysqli_error($con)."');";
          echo "</script>";
        }
      }
    }
  } else if ($meal_action == 0) {
    $mealid = $_POST["mealid"];
    $enable_acccode = mysqli_real_escape_string($con, $_POST['enable-acccode']);
    $enable_pw = mysqli_real_escape_string($con, $_POST['enable-pw']);
    $meal_edit_brand = mysqli_real_escape_string($con, $_POST['meal-edit-brand']);
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
      // begin the process of editing the brand of the meal //
      while (list($key,$val) = @each ($mealid)) {
        // first we check the meal exists in database or not //
        $VERCODEEX = "SELECT * FROM meal
                      WHERE meal_id = $val";
        $VERCODEEXQ = mysqli_query($con, $VERCODEEX);
        if (mysqli_num_rows($VERCODEEXQ) < 1) {
          // if we are unable to retrieve the data of the meal then something must gone wrong //
          echo "<script>alert('WARNING: Unable to retrieve the data of the meal. Please try again.');";
          echo "</script>";
        } else {
          // if the meal existing in the database then we will start to edit the meal's brand //
          $ENABLEVER = "UPDATE meal SET meal_brand_id = $meal_edit_brand WHERE meal_id = ?";
          // then we will start to update the information //
          $stmt = $con->prepare($ENABLEVER);
          $stmt->bind_param("i", $val);
          $stmt->execute();

          if (($stmt->error) == FALSE) {
            // if there is no error then close the previous statement //
            $stmt->close();
            // if we have reached this point then the update process is a success //
            echo "<script>alert('Notice: Update Successful for Meal ID: ".$val.".');";
            echo "</script>";
          } else {
            // if we are unable to update the meal then something must gone wrong //
            echo "<script>alert('WARNING: Unable to update the Meal ID: ".$val.". Please try again. Possible Error: ".mysqli_error($con)."');";
            echo "</script>";
          }
        }
      }
      // if the process is entirely over then alert the admin //
      echo "<script>alert('Notice: All enable process is now complete. Now returning to Admin Panel.');";
      echo "window.location.href='admin_panel.html';</script>";
    }
    //***END OF PROCESS***//
  } else {
    // if the action is undefine then //
    echo "<script>alert('Notice: Edit action undefined, please try again. Now returning to Admin Panel.');";
    echo "window.location.href='admin_panel.html';</script>";
  }
}
// End of the function: Verification Code's Verification Form: Enable //
?>
