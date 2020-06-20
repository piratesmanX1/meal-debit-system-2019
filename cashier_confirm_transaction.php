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
// first we will check whether there's already the same meal registered within the order id //
$CHECKBAL = " SELECT *
              FROM user INNER JOIN student
              ON user.user_id = student.user_id
              WHERE account_id = '".$_POST["account_id"]."' AND user.active = 1";
$CHECKBALQ = mysqli_query($con, $CHECKBAL);
if (mysqli_num_rows($CHECKBALQ) < 1) {
  // if there's none of data retrieve then either the account ID is wrong or there's no such active user //
  echo "Notice: Account ID invalid, or there\'s no such active user within the database.";
} else {
  // if we're able to retrieve the related info then we will call the balance into $_SESSION //
  if ($row = mysqli_fetch_array($CHECKBALQ)) {
    if ((isset($row['balance'])) && ($row['balance']) != NULL) {
      $_SESSION["balance"] = $row['balance'];
      $convert = $_SESSION["balance"];
      $_SESSION["balance"] = number_format((float)$convert, 2, '.', '');
      // define the buyer_id //
      $_SESSION["buyer_id"] = $row['user_id'];
      // then we check the the balance is higher than the total cost of the meal or not //
      if (($_SESSION["balance"]) >= ($_SESSION["subtotal"])) {
        // if it is then we will perform the queries to confirm the transaction //
        // first we find how much the balance left after deduction //
        $balance_amount = ($_SESSION["balance"] - $_SESSION["subtotal"]);
        // it's negative since it's deduction //
        $update_amount = "-".$_SESSION["subtotal"];
        // update_method is 1 as 1 stands for transaction, 0 stands for top up //
        $update_method = 1;
        // defining current time //
        $present = date_create();
        // Taking the current time //
        date_default_timezone_set("Etc/GMT-8");
        $now = date("Y-m-d H:i:s");

        // now begin the confirm transaction process //
        $UPDATEBAL = "INSERT INTO balance_record (`update_amount`, `update_date`, `update_method`, `balance_amount`, `user_id`)
                      VALUES ('$update_amount','$now','$update_method','$balance_amount','".$_SESSION["buyer_id"]."')";
        $UPDATEBALQ = mysqli_query($con, $UPDATEBAL);
        if (mysqli_affected_rows($con) < 1) {
      		// if there's no affected rows, then we will tell the user about it //
      		echo "WARNING: Order List ID: ".$_SESSION["order_id"]."\'s content unable to insert into balance_record table. Possible Error: ".mysqli_error($con).".";
      	} else {
          // once it's inserted we will have to update the related values in the tables: meal, student and user_order table //
          // we will first update the content of user_order, update to state that the related order has paid, the total price together of the meal cost, the date of transaction, and buyer_id who bought it //
          $UPDATEORD = "UPDATE user_order
                        SET user_id = '".$_SESSION["buyer_id"]."', transaction_date = '".$now."', total_price = '".$_SESSION["subtotal"]."', paid = 1
                        WHERE order_id = '".$_SESSION["order_id"]."'";
          $UPDATEORDQ = mysqli_query($con, $UPDATEORD);
          if (mysqli_affected_rows($con) < 1) {
        		// if there's no affected rows, then we will tell the user about it //
        		echo "WARNING: Order List ID: ".$_SESSION["order_id"]."\'s content unable to be updated. Possible Error: ".mysqli_error($con).".";
        	} else {
            // if it's a success then we will start to update the table: meal //
            $UPDATEMEA = "UPDATE meal INNER JOIN transaction_record
                          ON meal.meal_id = transaction_record.meal_id
                          SET meal.meal_quantity = (meal.meal_quantity - transaction_record.meal_quantity_cart)
                          WHERE order_id = '".$_SESSION["order_id"]."'";
            $UPDATEMEAQ = mysqli_query($con, $UPDATEMEA);
            if (mysqli_affected_rows($con) < 1) {
          		// if there's no affected rows, then we will tell the user about it //
          		echo "WARNING: Order List ID: ".$_SESSION["order_id"]."\'s meal quantity unable to deduct on their related Meal ID. Possible Error: ".mysqli_error($con).".";
          	} else {
              // now finally, update the student's balance //
              $UPDATESTUD = "UPDATE student
                             SET balance = '".$balance_amount."'
                             WHERE user_id = '".$_SESSION["buyer_id"]."'";
              $UPDATESTUDQ = mysqli_query($con, $UPDATESTUD);
              if (mysqli_affected_rows($con) < 1) {
            		// if there's no affected rows, then we will tell the user about it //
            		echo "WARNING: Unable to update the balance on User ID: ".$_SESSION["buyer_id"].". Possible Error: ".mysqli_error($con).".";
            	} else {
                // if we've reached this point then the update process is now completed //
                echo "Notice: Transaction Order ID: ".$_SESSION["order_id"]." confirmed and processed.";
                // now reset every $_SESSION back to default //
                $_SESSION["brand_id"] = "";
                $_SESSION["buyer_id"] = "";
                $_SESSION["order_id"] = "";
                $_SESSION["balance"] = "";
                $_SESSION["subtotal"] = "";
              }
            }
          }
        }
      } else {
        // if it is not eligible then we inform the user about it //
        echo "Notice: Student Balance of User ID: ".$_SESSION['buyer_id']." is not eligible as the balance has only RM ".$_SESSION['balance'].", which is lower than the total cost of meal RM ".$_SESSION['subtotal'].".";
      }
    } else {
      $_SESSION["balance"] = 0.00;
      echo "Notice: Student Balance NOT imported since it might be NULL or it\'s empty. It\'ll be treated as default 0 balance, therefore the transaction is not eligible.";
    }
  }
}
?>
