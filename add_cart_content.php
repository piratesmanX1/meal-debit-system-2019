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
// before allowing to add to cart, we have to verify whether the meal_quantity in our cart list is over the original quantity of the meal or not //
$CHECKQUAN = "SELECT *
              FROM meal INNER JOIN transaction_record
              ON meal.meal_id = transaction_record.meal_id
              WHERE transaction_record.order_id = '".$_SESSION["order_id"]."' AND transaction_record.meal_id = '".$_SESSION["meal_id"]."'";
$CHECKQUANQ = mysqli_query($con, $CHECKQUAN);
if (mysqli_num_rows($CHECKQUANQ) < 1) {
  // if we can't retrieve the data then something is wrong, and inform the user about it //
  // it might be possible that user still didnt add the related meal yet //
  // if there's none of the same meal registered within the transaction_record then we will insert a new one //
  $sql = "INSERT INTO transaction_record(meal_brand_id, meal_quantity_cart, meal_id, order_id)
          VALUES('".$_SESSION["brand_id"]."', '".$_POST["meal_quantity"]."', '".$_SESSION["meal_id"]."', '".$_SESSION["order_id"]."')";
  if(mysqli_query($con, $sql)) {
       echo 'Notice: Meal ID '.$_SESSION["meal_id"].' inserted into Order ID: '.$_SESSION["order_id"].'.';
  }
} else {
  if ($row = mysqli_fetch_array($CHECKQUANQ)) {
    // now, taking in the value and compare //
    if (($row["meal_quantity_cart"] + $_POST["meal_quantity"]) <= $row["meal_quantity"]) {
      // the default quantity of meal is bigger than the current meal_quantity in cart and the intended addition number of the meal to the cart then allow the new update //
      // first we will check whether there's already the same meal registered within the order id //
      $CHECKMEAL = "SELECT * FROM transaction_record
                    WHERE order_id = '".$_SESSION["order_id"]."' AND meal_id = '".$_SESSION["meal_id"]."'";
      $CHECKMEALQ = mysqli_query($con, $CHECKMEAL);
      if (mysqli_num_rows($CHECKMEALQ) < 1) {
        // if there's none of the same meal registered within the transaction_record then we will insert a new one //
        $sql = "INSERT INTO transaction_record(meal_brand_id, meal_quantity_cart, meal_id, order_id)
                VALUES('".$_SESSION["brand_id"]."', '".$_POST["meal_quantity"]."', '".$_SESSION["meal_id"]."', '".$_SESSION["order_id"]."')";
        if(mysqli_query($con, $sql)) {
             echo 'Notice: Meal ID '.$_SESSION["meal_id"].' inserted into Order ID: '.$_SESSION["order_id"].'.';
        }
      } else {
        // if there is already registered transaction_record then we will update the quantity of the registered meal quantity //
        // Formula: Registered Meal Quantity + New Quantity //
        $sql = "UPDATE transaction_record SET meal_quantity_cart = (meal_quantity_cart + '".$_POST["meal_quantity"]."')
                WHERE meal_id = '".$_SESSION["meal_id"]."' AND order_id = '".$_SESSION["order_id"]."'";
        if(mysqli_query($con, $sql)) {
             echo 'Notice: Meal quantity of Meal ID '.$_SESSION["meal_id"].' in Order ID: '.$_SESSION["order_id"].' is updated.';
        }
      }
    } else {
      echo "Notice: Meal Quantity that you intended to add into the cart list exceeds the current amount of Meal Quantity. Try a smaller value.";
    }
  }
}
?>
