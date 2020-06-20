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
// defining the variables for showing the info of the order //
if ((isset($_SESSION['brand_id'])) && (($_SESSION['brand_id']) !== "")) {
  // starts to find the brand's name based on the id //
  $BRANNAME = "SELECT brand_name FROM meal_brand WHERE brand_id = '".$_SESSION["brand_id"]."'";
  $BRANNAMEQ = mysqli_query($con, $BRANNAME);
  if (mysqli_num_rows($BRANNAMEQ) < 1) {
    $brand_name = " - ";
  } else {
    if ($row = mysqli_fetch_array($BRANNAMEQ)) {
      $brand_name = $row['brand_name'];
    }
  }
} else {
  $brand_name = " - ";
}

?>

<!-- Pop up Cart Style -->
<div id="id01" class="modal" style="font-family:Oswald!important;">
      <main class="page modal-content animate">
	 	<section class="shopping-cart dark">
	 		<div class="container">
		        <div class="block-heading">
              <img src="image/logo_2.png" alt="logo" style="height:60px!important;width:120px!important;" />
		          <h2></h2>
		          <p>Cart List</p>
		        </div>
		        <div class="content">
	 				<div class="row">
	 					<div class="col-md-12 col-lg-8">
	 						<div class="items">

<?php
// begin to print out the related content that exists in the transaction_record table //
$TRANREC = "SELECT *
            FROM transaction_record INNER JOIN meal
            ON transaction_record.meal_id = meal.meal_id
            WHERE transaction_record.order_id = '".$_SESSION['order_id']."'";
$TRANSRECQ = mysqli_query($con, $TRANREC);
$result = $con->query($TRANREC);
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    // now define the path of the image //
    if (isset($row["brand_image"])) {
      $meal_image = $row["meal_image"];
    } else {
      $meal_image = "/APU/SDP/image/e3.png";
    }
    // perform the calculation for the total price of the specific meal //
    // Formula: Meal Quantity * Meal Price //
    if ((isset($row["meal_price"])) && (isset($row["meal_quantity_cart"]))) {
      $meal_total_price = (($row["meal_price"]) * ($row["meal_quantity_cart"]));
    } else {
      $meal_total_price = "-";
    }
    // begin to call out the cart list //
    echo '
    <div class="product">
      <div class="row">
        <div class="buttons" style="padding-left: 20px!important;">
          <span class="delete-btn" style="padding-left: 50px!important;"></span>
        </div>
        <div class="col-md-3">
          <img class="img-fluid mx-auto d-block image" style="width:160px!important;height:160px!important;" src="'.$meal_image.'">
        </div>
        <div class="col-md-8">
          <div class="info">
            <div class="row">
              <div class="col-md-5 product-name">
                <div class="product-name">
                  <a href="#"> '.$row["meal_name"].' </a>
                  <div class="product-info">
                    <div>Brand: <span class="value"> '.$brand_name.' </span></div>
                    <div>Stock: <span class="value"> '.$row["meal_quantity"].' </span></div>
                  </div>
                </div>
              </div>
              <div class="col-md-4 quantity">
                <label for="quantity">Quantity:</label>
                <input id="quantity" type="number" value ="'.$row["meal_quantity_cart"].'" min="1" step="1" class="form-control quantity-input">
              </div>
              <div class="col-md-3 price">
                <span> RM '.$meal_total_price.' </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    ';
  }
}
?>

            </div>
          </div>
            <div class="col-md-12 col-lg-4">
              <div class="summary">
                <h3>Summary</h3>
                <div class="summary-item"><span class="text">Subtotal</span><span class="price">$360</span></div>
                <div class="summary-item"><span class="text">Discount</span><span class="price">$0</span></div>
                <div class="summary-item"><span class="text">Shipping</span><span class="price">$0</span></div>
                <div class="summary-item"><span class="text" style="padding-top: 20px!important;">Total</span><span class="price">$360</span></div>
                <button type="button" class="btn btn-primary btn-lg btn-block">Checkout</button>
                <button type="button" class="btn btn-primary btn-lg btn-block" style="background-color:red!important;">Cancel Order</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
</div>
