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
if ((isset($_SESSION["order_id"])) && ($_SESSION["order_id"] != "")) {
  // before removing the meal from the cart list, we have to update the related Foreign Keys to NULL due to SYNTAX //
  // first we will get the transaction_id first so in the latter process of deletion it will be useful //
  $CHECKMEAL = "SELECT * FROM transaction_record
                WHERE order_id = '".$_SESSION["order_id"]."'";
  $CHECKMEALQ = mysqli_query($con, $CHECKMEAL);
  if (mysqli_num_rows($CHECKMEALQ) < 1) {
  	// if we can't retrive the info then we will inform the user //
  	echo "WARNING: Meal ID's data unable to be retrieved. Possible Error: ".mysqli_error($con);
  } else {
    $result = $con->query($CHECKMEAL);
    if ($result->num_rows > 0) {
      // if there's a result then begin to put the related transaction id into the variable //
      while ($row = $result->fetch_assoc()) {
        $transaction_id[] = $row["transaction_id"];
      }
    }
  	// once we got the related transaction_id, we will update the related Foreign Keys: order_id, meal_brand_id, and meal_id to NULL in transaction_record table //
  	$TRANSNULL = "UPDATE transaction_record SET order_id = NULL, meal_brand_id = NULL, meal_id = NULL
  	              WHERE order_id = '".$_SESSION["order_id"]."'";
  	$TRANSNULLQ = mysqli_query($con, $TRANSNULL);
  	if (mysqli_affected_rows($con) < 1) {
  		// if there's no affected rows, then we will tell the user about it //
  		echo "WARNING: Meal ID's data unable to be updated. Possible Error: ".mysqli_error($con).".";
  	} else {
  		// once we updated all the Foreign Keys to NULL, we will start the deletion process //
      while (list($key,$val) = @each ($transaction_id)) {
        $DELETETRANS = "DELETE FROM transaction_record WHERE transaction_id = $val";
        $DELETETRANSQ = mysqli_query($con, $DELETETRANS);
        if (mysqli_affected_rows($con) < 1) {
          // if we are unable to delete the transaction record, something must gone wrong //
          echo "WARNING: Unable to delete the Transaction ID: ".$val.". Please try again. Possible Error: ".mysqli_error($con);
        } else {
          //echo "Notice: Removed Meal's Transaction ID: ".$val;//
        }
      }
      // once deleted all the transaction record, we will start to delete the related order_id as well //
      // but first, update all Foreign Keys to NULL: user_id, brand_id, and cashier_id //
      $UPDATENULL = "UPDATE user_order SET user_id = NULL, brand_id = NULL, cashier_id = NULL WHERE order_id = '".$_SESSION["order_id"]."'";
      $UPDATENULLQ = mysqli_query($con, $UPDATENULL);
      if (mysqli_affected_rows($con) < 1) {
        // if we are unable to update the user_order, something must gone wrong //
        echo "WARNING: Unable to update the Order ID: '".$_SESSION["order_id"]."'. Please try again. Possible Error: ".mysqli_error($con);
      } else {
        // we will start to delete the order_id once all the Foreign Keys have set to NULL //
        $DELETEORD = "DELETE FROM user_order WHERE order_id = '".$_SESSION["order_id"]."'";
        $DELETEORDQ = mysqli_query($con, $DELETEORD);
        if (mysqli_affected_rows($con) < 1) {
          // if we are unable to update the user_order, something must gone wrong //
          echo "WARNING: Unable to delete the Order ID: '".$_SESSION["order_id"]."'. Please try again. Possible Error: ".mysqli_error($con);
        } else {
          // once we've reached this point we've removed/deleted the entire cart data //
          $old_order_id = $_SESSION['order_id'];
          // first create an order_id in the database, then retrieve it's primary key //
          $INSERTORDER = "INSERT INTO user_order
                          (`paid`, `brand_id`, `cashier_id`)
                          VALUES (0, '".$_SESSION["brand_id"]."', '".$_SESSION["user_id"]."')";
          $INSERTORDERQ = mysqli_query($con, $INSERTORDER);
          if (mysqli_affected_rows($con) < 1) {
            // if there's no result then inform the user and show the possible SQL error //
            echo "WARNING: Order ID can't be generated. Possible Error: ".mysqli_error($con);
          } else {
            // if we're able to retrieve the primary key then assign it into the $_SESSION variable //
            $_SESSION["order_id"] = mysqli_insert_id($con);
            $_SESSION['cart_numb'] = "0";
            echo "Notice: Order ID: ".$old_order_id." removed, and a new Order ID: ".$_SESSION['order_id']." is generated in place.";
          }
        }
      }
  	}
  }
} else {
  echo "WARNING: Order ID undefined, something must gone wrong. Please login and logout to try again.";
}
?>
