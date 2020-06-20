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
// before removing the meal from the cart list, we have to update the related Foreign Keys to NULL due to SYNTAX //
// first we will get the transaction_id first so in the latter process of deletion it will be useful //
$CHECKMEAL = "SELECT * FROM transaction_record
              WHERE order_id = '".$_SESSION["order_id"]."' AND meal_id = '".$_POST["meal_id"]."'";
$CHECKMEALQ = mysqli_query($con, $CHECKMEAL);
if (mysqli_num_rows($CHECKMEALQ) < 1) {
	// if we can't retrive the info then we will inform the user //
	echo "WARNING: Meal ID: ".$_POST['meal_id']."\'s data unable to be retrieved. Possible Error: ".mysqli_error($con);
} else {
	// if we are able to retrieve the data then we will set the transaction_id into a variable for the latter process //
	if ($row = mysqli_fetch_array($CHECKMEALQ)) {
		$transaction_id = $row['transaction_id'];
	}
	// once we got the related transaction_id, we will update the related Foreign Keys: order_id, meal_brand_id, and meal_id to NULL in transaction_record table //
	$TRANSNULL = "UPDATE transaction_record SET order_id = NULL, meal_brand_id = NULL, meal_id = NULL
	              WHERE order_id = '".$_SESSION["order_id"]."' AND meal_id = '".$_POST["meal_id"]."'";
	$TRANSNULLQ = mysqli_query($con, $TRANSNULL);
	if (mysqli_affected_rows($con) < 1) {
		// if there's no affected rows, then we will tell the user about it //
		echo "WARNING: Meal ID: ".$_POST['meal_id']."\'s data unable to be updated. Possible Error: ".mysqli_error($con).".";
	} else {
		// once we updated all the Foreign Keys to NULL, we will start the deletion process //
		$sql = "DELETE FROM transaction_record WHERE transaction_id = '".$transaction_id."'";
		if(mysqli_query($con, $sql)) {
			echo 'Notice: Meal removed from cart list.';
		}
	}
}

?>
