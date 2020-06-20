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
// Taking the current time //
date_default_timezone_set("Etc/GMT-8");
$now = date("Y-m-d H:i:s");
// convert datetime into 12AM/PM format //
$now = date('d/m/y h:i A', strtotime($now));

// define the reason why this account is disabled //
$reason = "Account owner disabled the account by himself on ".$now;
$DISUSER = "UPDATE user SET active = 0, suspended_reason = '".$reason."' WHERE user_id = '".$_POST["user_id"]."'";
$DISUSERQ = mysqli_query($con, $DISUSER);
if (mysqli_affected_rows($con) < 1) {
	// if we can't disable the user then something must gone wrong //
	echo "WARNING: User ID: ".$_POST["user_id"]." unable to be disabled. Possible Error: ".mysqli_error($con);
} else {
	echo "Notice: Account Disabled.";
  // logout function specifically for cashier //
  if (isset($_SESSION['order_id']) && (($_SESSION['order_id']) != "")) {
    // begin the query of deletion of the related order, and clearing the $_SESSION back to empty string, then replace them with the new value //
    // but first we have to update the Foreign Key of the related table to NULL in order to delete the order due to SYNTAX //
    $UPDATENULL = "UPDATE user_order SET user_id = NULL, brand_id = NULL, cashier_id = NULL WHERE order_id = '".$_SESSION["order_id"]."' AND paid = 0";
    $UPDATENULLQ = mysqli_query($con, $UPDATENULL);
    if (mysqli_affected_rows($con) < 1) {
      // if there's no result then inform the user and show the possible SQL error //
      echo "<script>alert('WARNING: Order ID can\'t be updated, you will be return back to Cashier Panel. Possible Error: ".mysqli_error($con)."');";
      echo "window.location.href='cashier_panel.html';</script>";
    } else {
      // if we're able to update the Foreign Key, proceeds to another related table to update their Foreign Key as well //
      // echo "<script>alert('Notice: Order ID: ".$_SESSION['order_id']." updated, now updating another table\'s Foreign Key in order to proceed the deletion...');</script>"; //
      // then we've to first get the transaction_id from the related records in transaction_record table so in latter process we will able to delete the record specifically //
      // the variable will be an array to hold all the transaction_id //
      $transaction_id = "";
      // defining and execute the query which finds all the related transaction records //
      $TRANSID = "SELECT * FROM transaction_record WHERE order_id = '".$_SESSION["order_id"]."'";
      $TRANSIDQ = mysqli_query($con, $TRANSID);
      if (mysqli_num_rows($TRANSIDQ) < 1) {
        // if we're able to retrieve the transaction_id, alert the user about it //
        // echo "<script>alert('WARNING: Unable to retrieve transaction_records under Order ID: ".$_SESSION['order_id'].", or there\'s no record existing in the transaction record table. Checking the types of error occured...');</script>"; //
        // if there's no record under transaction_record table, then we can straight delete the order_id //
        $DELETEORD = "DELETE FROM user_order WHERE paid < 1 AND order_id = '".$_SESSION["order_id"]."'";
        $DELETEORDQ = mysqli_query($con, $DELETEORD);
        if (mysqli_affected_rows($con) < 1) {
          // if there's no result then inform the user and show the possible SQL error //
          echo "<script>alert('WARNING: Order ID: ".$_SESSION['order_id']." can\'t be deleted, it\'s highly possible that the fault is on SQL Query. Possible Error: ".mysqli_error($con)."');";
          echo "window.location.href='cashier_panel.html';</script>";
        } else {
          // if the deletion is a success then exit() immediately, and declare the related $_SESSION to empty string //
          $_SESSION['brand_id'] = "";
          $_SESSION['order_id'] = "";
          $_SESSION['buyer_id'] = "";
          // echo "<script>alert('Notice: Deletion Process completed. Continue to proceed your intended progress.');"; //
          // echo "window.location.href='cashier_panel.html';</script>"; //
        }
      } else {
        $result = $con->query($TRANSID);
        if ($result->num_rows > 0) {
          // if there's a result then begin to put the related transaction id into the variable //
          while ($row = $result->fetch_assoc()) {
            $transaction_id[] = $row["transaction_id"];
          }
        }
        // once we got all the transaction_id then we proceed to update the transaction_record table //
        $UPDATENULL = "UPDATE transaction_record SET meal_id = NULL, meal_brand_id = NULL, order_id = NULL WHERE order_id = '".$_SESSION["order_id"]."'";
        $UPDATENULLQ = mysqli_query($con, $UPDATENULL);
        if (mysqli_affected_rows($con) < 1) {
          // if there's no result then inform the user and show the possible SQL error //
          echo "<script>alert('WARNING: Transaction Record ID can\'t be updated, you will be return back to Cashier Panel. Possible Error: ".mysqli_error($con)."');";
          echo "window.location.href='cashier_panel.html';</script>";
        } else {
          // if we've updated the table, begin the process of deletion //
          // echo "<script>alert('Notice: Transaction Record ID updated, now begin the deletion of the related information.');</script>"; //
          $DELETEORD = "DELETE FROM user_order WHERE paid < 1 AND order_id = '".$_SESSION["order_id"]."'";
          $DELETEORDQ = mysqli_query($con, $DELETEORD);
          if (mysqli_affected_rows($con) < 1) {
            // if there's no result then inform the user and show the possible SQL error //
            echo "<script>alert('WARNING: Order ID: ".$_SESSION['order_id']." can\'t be deleted, you will be return back to Cashier Panel. Possible Error: ".mysqli_error($con)."');";
            echo "window.location.href='cashier_panel.html';</script>";
          } else {
            // if we've deleted the related info in order table, begin the final deletion on transaction_record table //
            // echo "<script>alert('Notice: Order ID: ".$_SESSION['order_id']." deleted, now begin the deletion of the related information.');</script>"; //
            while (list($key,$val) = @each ($transaction_id)) {
              $DELETETRANS = "DELETE FROM transaction_record WHERE transaction_id = $val";
              $DELETETRANSQ = mysqli_query($con, $DELETETRANS);
              if (mysqli_affected_rows($con) < 1) {
                // if we are unable to delete the transaction record, something must gone wrong //
                echo "WARNING: Unable to delete the Transaction ID: ".$val.". Please try again. Possible Error: ".mysqli_error($con);
              } else {
                echo "Deleted Transaction ID: ".$val;
              }
            }
            // if we have reached this point then deletion process is completed, and begin to revert the $_SESSION back to empty string //
            $_SESSION['brand_id'] = "";
            $_SESSION['order_id'] = "";
            $_SESSION['buyer_id'] = "";
          }
        }
      }
    }
  }
    session_destroy();
    exit();
}

?>
