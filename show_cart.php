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
// before we begin we have to see whether there's an order_id within the $_SESSION or not //
if ((isset($_SESSION['order_id'])) && (($_SESSION['order_id']) !== "")) {
  // begin to print out the related content that exists in the transaction_record table //
  // but first we have to check whether there's transaction record exists within the table first //
  // since it'll return NULL values which is considered as 1 num_row, we've to seperately search the related value //
  $TRANEX = "SELECT * FROM transaction_record WHERE order_id = '".$_SESSION['order_id']."'";
  $TRANEXQ = mysqli_query($con, $TRANEX);
  if (mysqli_num_rows($TRANEXQ) < 1) {
    // if there's no result within the transaction_record table then we will show an empty table //
    $meal_image = "/APU/SDP/image/e3.png";
    // defining the number of records //
    $cart_numb = 0;
    echo '
    <div class="product">
      <div class="row">
        <div class="buttons" style="padding-left: 20px!important;">
          <span class="delete-btn" style="padding-left: 50px!important;"></span>
        </div>
        <div class="col-md-3">
          <img class="img-fluid mx-auto d-block image" style="width:160px!important;height:160px!important;padding:20px;!important" src="'.$meal_image.'">
        </div>
        <div class="col-md-8">
          <div class="info">
            <div class="row">
              <div class="col-md-5 product-name">
                <div class="product-name">
                  <a href="#"> - </a>
                  <div class="product-info">
                    <div>Brand: <span class="value"> - </span></div>
                    <div>Stock: <span class="value"> - </span></div>
                  </div>
                </div>
              </div>
              <div class="col-md-4 quantity">
                <label for="quantity">Quantity:</label>
                <input id="quantity" type="number" disabled value="0" step="1" class="form-control quantity-input">
              </div>
              <div class="col-md-3 price">
                <span> RM 0 </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    ';
    $totalcost = 0;
    echo '<center><span style="color:red!important">Notice: You still didn\'t order anything. </span></center>';
    $_SESSION["cart_numb"] = 0;
  } else {
    // taking in the values //
    $TRANREC = "SELECT *
                FROM transaction_record INNER JOIN meal
                ON transaction_record.meal_id = meal.meal_id
                WHERE transaction_record.order_id = '".$_SESSION['order_id']."'";
    $TRANSRECQ = mysqli_query($con, $TRANREC);
    $result = $con->query($TRANREC);
    if ($result->num_rows > 0) {
      // now defining the total price of the entire cost //
      $totalcost = 0;
      // defining the number of records //
      $cart_numb = 0;
      while ($row = $result->fetch_assoc()) {
        // now define the path of the image //
        if (isset($row["meal_image"])) {
          $meal_image = $row["meal_image"];
        } else if (empty($row["meal_image"])) {
          $meal_image = "/APU/SDP/image/e1.png";
        } else {
          $meal_image = "/APU/SDP/image/e3.png";
        }
        // perform the calculation for the total price of the specific meal //
        // Formula: Meal Quantity * Meal Price //
        if ((isset($row["meal_price"])) && (isset($row["meal_quantity_cart"]))) {
          $meal_total_price = (($row["meal_price"]) * ($row["meal_quantity_cart"]));
          $meal_total_price = number_format((float)$meal_total_price, 2, '.', '');
        } else {
          $meal_total_price = "-";
        }
        // begin to call out the cart list //
        echo '
        <div class="product">
          <div class="row">
            <div class="buttons" style="padding-left: 20px!important;">
              <span class="delete-btn delete_cart" style="padding-left: 50px!important;" data-id1="'.$row["meal_id"].'"></span>
            </div>
            <div class="col-md-3">
              <img class="img-fluid mx-auto d-block image" style="width:160px!important;height:160px!important;padding:20px;!important" src="'.$meal_image.'">
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
                    <input id="quantity" type="number" class="meal_quantity_list" data-id2="'.$row["meal_id"].'" value ="'.$row["meal_quantity_cart"].'" min="1" step="1" max="'.$row["meal_quantity"].'" onKeyDown="return false" class="form-control quantity-input">
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
        $totalcost = $totalcost + $meal_total_price;
        $totalcost = number_format((float)$totalcost, 2, '.', '');
        $cart_numb++;
      }
      $_SESSION["cart_numb"] = $cart_numb;
    } else {
      // if there's no result then //
      // defining the number of records //
      $cart_numb = 0;
      $meal_image = "/APU/SDP/image/e1.png";
      echo '
      <div class="product">
        <div class="row">
          <div class="buttons" style="padding-left: 20px!important;">
            <span class="delete-btn" style="padding-left: 50px!important;"></span>
          </div>
          <div class="col-md-3">
            <img class="img-fluid mx-auto d-block image" style="width:160px!important;height:160px!important;padding:20px;!important" src="'.$meal_image.'">
          </div>
          <div class="col-md-8">
            <div class="info">
              <div class="row">
                <div class="col-md-5 product-name">
                  <div class="product-name">
                    <a href="#"> - </a>
                    <div class="product-info">
                      <div>Brand: <span class="value"> - </span></div>
                      <div>Stock: <span class="value"> - </span></div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 quantity">
                  <label for="quantity">Quantity:</label>
                  <input id="quantity" type="number" value ="0" disabled min="1" step="1" class="form-control quantity-input">
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
      $totalcost = 0;
      echo '
      <center><span style="color:red!important">Notice: No records found under the Order ID. </span></center>
      ';
      $_SESSION["cart_numb"] = $cart_numb;
    }
  }
} else {
  // if there's no order_id then we tell the cashier about it //
  // defining the number of records //
  $cart_numb = 0;
  $meal_image = "/APU/SDP/image/e1.png";
  echo '
  <div class="product">
    <div class="row">
      <div class="buttons" style="padding-left: 20px!important;">
        <span class="delete-btn" style="padding-left: 50px!important;"></span>
      </div>
      <div class="col-md-3">
        <img class="img-fluid mx-auto d-block image" style="width:160px!important;height:160px!important;padding:20px;!important" src="'.$meal_image.'">
      </div>
      <div class="col-md-8">
        <div class="info">
          <div class="row">
            <div class="col-md-5 product-name">
              <div class="product-name">
                <a href="#"> - </a>
                <div class="product-info">
                  <div>Brand: <span class="value"> - </span></div>
                  <div>Stock: <span class="value"> - </span></div>
                </div>
              </div>
            </div>
            <div class="col-md-4 quantity">
              <label for="quantity">Quantity:</label>
              <input id="quantity" type="number" disabled value="0" step="1" class="form-control quantity-input">
            </div>
            <div class="col-md-3 price">
              <span> RM - </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  ';
  $totalcost = 0;
  echo '
  <center><span style="color:red!important">Notice: Order ID yet to be defined. </span></center>
  ';
  $_SESSION["cart_numb"] = 0;
}
?>

<?php
$TRANEX = "SELECT * FROM transaction_record WHERE order_id = '".$_SESSION['order_id']."'";
$TRANEXQ = mysqli_query($con, $TRANEX);
echo
'
            </div>
          </div>
            <div class="col-md-12 col-lg-4">
              <div class="summary">
                <h3>Summary</h3>
                <div class="summary-item"><span class="text">Subtotal</span><span class="price"> RM '.$totalcost.' </span></div>
                <div class="summary-item"><span class="text">Discount</span><span class="price"> RM 0 </span></div>
                <div class="summary-item"><span class="text">Total</span><span class="price"> RM '.$totalcost.' </span></div>
                <div class="summary-item"></div>
                <button type="button" class="btn-trans btn-primary btn-lg btn-block" id="conf-trans" onclick="fetch_data();confirmTrans();"';
                if (mysqli_num_rows($TRANEXQ) < 1) {
                  echo "disabled";
                }
                echo
                '>Checkout</button>
                <button type="button" class="btn-trans btn-primary btn-lg btn-block delete_order" ';
                if (mysqli_num_rows($TRANEXQ) < 1) {
                  echo "disabled";
                }
                echo
                ' style="background-color:red!important;">Cancel Order</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
</div>
';

// inserting the value of records every second for the latest record //
echo '
<!-- Making the number of notification always get the latest info -->
<script>
function cartNumb() {
  document.getElementById("meal-notification").innerHTML = "'.$_SESSION['cart_numb'].'";
}
window.setInterval(function(){
/// call your function here
  cartNumb()
}, 1000);

window.onload = cartNumb;
</script>
';
?>
