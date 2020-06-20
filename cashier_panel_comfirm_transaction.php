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

// start to define the value of the order_id //
if ((isset($_SESSION['order_id'])) && (($_SESSION['order_id']) !== "")) {
  $order_id = $_SESSION['order_id'];
} else {
  $order_id = " - ";
}
?>

<?php
// navigation bar section //
echo '
<nav class="mainmenu-area affix" data-spy="affix" data-offset-top="200">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#primary_menu">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">
				<img src="image/logo.png" alt="Logo"></a>
            </div>
            <div class="collapse navbar-collapse" id="primary_menu">
                <ul class="nav navbar-nav mainmenu">
                    <li><a href="homepage.html">Homepage</a></li>
                    <li><a href="cashier_panel.html#trending" onclick="branList(this)">Trending</a></li>
                    <li><a href="cashier_panel.html#brand" onclick="branList(this)">Brands</a></li>
                    <!--<li><a href="cashier_meal_panel.html">Meal Panel</a></li>-->
                </ul>
                <!-- due to Syntax contradiction we have to put onclick javascript outside PHP -->
                <i class="fa fa-shopping-cart" aria-hidden="true" style="position: absolute;right: 16vw;color: black;top: 36px; cursor:pointer!important;"';
?>
onclick="document.getElementById('id01').style.display='block'"

<?php
                // including popup cart-list //
                echo '<div id="cart-list">';
                  include "pop-up-cart.php";
                echo '</div>';
                echo '
                  <span class="badge" style="background-color: green!important;position: absolute;top:-1vh!important;right:-1vw!important;font-family:Oswald!important;" id="meal-notification" onclick="cartList()">
                  <!-- Showing the number of registering user -->
                  -
                  </span>
                </i>
                <div class="right-button hidden-xs">
                  <div class="navbar">
                    <div class="dropdown">
                      <img src="'.$_SESSION["profile_image"].'" alt="profile-pic" style="border-radius:50%!important; width:50px!important; height:50px!important;"/>
                      <button class="dropbtn">'.$_SESSION["first_name"].' '.$_SESSION["last_name"].'
                        <i class="fa fa-caret-down"></i>
                      </button>
                      <div class="dropdown-content">
                        <a href="profile_page.html">PROFILE PAGE</a>
                        <!--<a href="cashier_meal_panel.html">MEAL PANEL</a>-->
                        <a href="cashier_panel.html#brand" onclick="branList(this)">BRAND LIST</a>
                        <a class="logout" href="logout.php">LOG OUT <i class="fas fa-sign-out-alt"></i></a>
                        <a style="background-color:black!important; cursor:none!important;color:red!important"> ORDER ID: '.$order_id.'</a>
                        <a style="background-color:black!important; cursor:none!important;color:red!important"> BRAND: '.$brand_name.'</a>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </nav>
    <!-- MainMenu-Area-End -->';
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js" type="text/javascript"></script>
<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script  src="vendor/responsive-table-less-shopping-cart/js/index.js"></script>

<?php
echo '
<div class="wrap cf">
  <center><img src="image/logo_symbol.png" alt="logo" style="margin-top:60px!important"></center>
  <h1 class="projTitle"> Meal-Debit System <span>Cart-List</span></h1>
  <div class="heading cf">
    <h1>Confirm Transaction</h1>
    <a href="#" class="continue" onclick="mealBack(this)" value="'.$_SESSION["brand_id"].'">Continue Shopping</a>
  </div>
  <div class="cart">
<!--    <ul class="tableHead">
      <li class="prodHeader">Product</li>
      <li>Quantity</li>
      <li>Total</li>
       <li>Remove</li>
    </ul>-->
    <ul class="cartWrap">';

// begin to call out the content of the order_id //
// before we begin we have to see whether there's an order_id within the $_SESSION or not //
if ((isset($_SESSION['order_id'])) && (($_SESSION['order_id']) !== "")) {
  // begin to print out the related content that exists in the transaction_record table //
  // but first we have to check whether there's transaction record exists within the table first //
  // since it'll return NULL values which is considered as 1 num_row, we've to seperately search the related value //
  $TRANEX = "SELECT * FROM transaction_record WHERE order_id = '".$_SESSION['order_id']."'";
  $TRANEXQ = mysqli_query($con, $TRANEX);
  if (mysqli_num_rows($TRANEXQ) < 1) {
    // if there's no result within the transaction_record table then we will show an empty table //
    $meal_image = "/APU/SDP/image/e1.png";
    // defining the number of records //
    $cart_numb = 0;
    echo '
    <li class="items odd">
      <div class="infoWrap">
          <div class="cartSection">
          <img src="'.$meal_image.'" alt="" class="itemImg" style="width:128px!important;height:112px!important;"/>
            <p class="itemNumber">#000000</p>
            <h3> - </h3>
             <p> <input type="number" disabled class="qty" placeholder="1" style="40px!important;display:inline!important;"/> x RM 0.00 </p>
            <p class="stockStatus"> - </p>
          </div>
          <div class="prodTotal cartSection">
            <p> RM 0.00 </p>
          </div>
                <div class="cartSection removeWrap">
             <a href="#" class="remove">x</a>
          </div>
        </div>
    </li>
    ';
    $subtotal = 0;
    echo '<center><span style="color:red!important">Notice: You still didn\'t order anything. </span></center>';
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
      $subtotal = 0;
      // defining the number of records //
      $cart_numb = 0;
      $total_rec = ($result->num_rows);
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
        echo '
        <li class="items odd">
          <div class="infoWrap">
              <div class="cartSection meal_details_section">
              <img src="'.$row["meal_image"].'" alt="" class="itemImg" style="width:128px!important;height:112px!important;"/>
                <p class="itemNumber">#0000'.$row["meal_id"].'</p>
                <h3 class="trans_meal_name">'.$row["meal_name"].'</h3>
                 <p> <input type="number" class="qty meal_quantity_list_conf" step="1" min="1" max="'.$row["meal_quantity"].'" value="'.$row["meal_quantity_cart"].'" data-id1="'.$row["meal_id"].'" placeholder="1" style="width:40px!important;display:inline!important;"/> x RM '.$row["meal_price"].'</p>
                <p class="stockStatus ';
                // define whether the meal still in stock or out of stock or not //
                if ($row["meal_quantity"] == 0) {
                  echo "out";
                }
                echo
                '">';
                if ($row["meal_quantity"] == 0) {
                  echo "Out of Stock";
                } else {
                  echo "In Stock";
                }
                echo
                '</p>
              </div>
              <div class="prodTotal cartSection total_price_section" style="min-width:192px!important;width="192px!important"">
                <p class="total_price"> RM '.$meal_total_price.' </p>
              </div>
                    <div class="cartSection removeWrap">
                 <a href="#" class="remove delete_cart_conf" data-id2="'.$row["meal_id"].'">x</a>
              </div>
            </div>';
      $cart_numb++;
      $subtotal = $subtotal + $meal_total_price;
      $subtotal = number_format((float)$subtotal, 2, '.', '');
      // to define when we will have to call out the specific HTML attributes //
      if ($cart_numb == ($total_rec)) {
        // when it reached the last item of the transaction_record, then we will call out a specific HTML attribute //
        echo '
        <div class="special"><div class="specialContent">Remember to check your balance before you purchase!</div></div>
      </li>
        ';
      } else {
        echo '</li>';
      }
     }
    }
  }
  $_SESSION["subtotal"] = $subtotal;
}
?>

<?php
echo '
      <!--<li class="items even">Item 2</li>-->

    </ul>
  </div>

  <div class="promoCode">
    <label for="promo" style="font-size: inherit!important;">Check Student Balance Here through ID: </label><label for="account_id"></label>
      <input type="password" name="account_id" id="stud_mail" maxlength="6" minlength="6" placholder="Account ID" style="width:80%!important;"/>
      <a href="#" class="btn-trans btn-trans_email check_balance" style="height: 24px!important; content:none!important;margin-top: 9px!important;"></a>
    <span style="color:';
    if ((isset($_SESSION["balance"])) && (($_SESSION["balance"]) != NULL)) {
      // now comparing with the total cost of the entire meal see whether it's eligible or not //
      if (($_SESSION["balance"]) >= ($_SESSION["subtotal"]) ) {
        echo "green";
        $disable = 0;
      } else {
        echo "red";
        $disable = 1;
      }
    } else if ((isset($_SESSION["balance"])) && (($_SESSION["balance"]) == 0)) {
      echo "red";
      $disable = 1;
    } else {
      echo "red";
      $disable = 1;
    }
    echo
    '"> STATUS: <span id="eligible_trans">';
    if ((isset($_SESSION["balance"])) && (($_SESSION["balance"]) != NULL) && (($_SESSION["balance"]) > 0)) {
      // now comparing with the total cost of the entire meal see whether it's eligible or not //
      if (($_SESSION["balance"]) >= ($_SESSION["subtotal"]) ) {
        echo "ELIGIBLE";
        $disable = 0;
      } else {
        echo "NOT ELIGIBLE";
        $disable = 1;
      }
    } else if ((isset($_SESSION["balance"])) && (($_SESSION["balance"]) == 0)) {
      echo "NOT ELIGIBLE";
      $disable = 1;
    } else {
      echo "NOT ELIGIBLE";
      $disable = 1;
    }
    echo
    '</span> </span><br>
    <span style="color:';
    if ((isset($_SESSION["balance"])) && (($_SESSION["balance"]) != NULL)) {
      // now comparing with the total cost of the entire meal see whether it's eligible or not //
      if (($_SESSION["balance"]) >= ($_SESSION["subtotal"])) {
        echo "green";
        $disable = 0;
      } else {
        echo "red";
        $disable = 1;
      }
    } else if ((isset($_SESSION["balance"])) && (($_SESSION["balance"]) == 0)) {
      echo "red";
      $disable = 1;
    } else {
      echo "red";
      $disable = 1;
    }
    echo
    '"> STUDENT BALANCE: RM <span id="stud_balance">';
    if ((isset($_SESSION["balance"])) && (($_SESSION["balance"]) != NULL)) {
      echo $_SESSION["balance"];
    } else {
      echo "0.00";
      $disable = 1;
    }
    echo
    '</span> </span>
  </div>

  <div class="subtotal cf">
    <ul>
      <li class="totalRow"><span class="label" style="background:none!important;">Subtotal</span><span class="value"> RM '.$subtotal.' </span></li>

          <li class="totalRow"><span class="label" style="background:none!important;">SST</span><span class="value"> RM0.00 </span></li>

            <li class="totalRow"><span class="label" style="background:none!important;">GST</span><span class="value"> RM0.00 </span></li>
            <li class="totalRow final"><span class="label" style="background:none!important;">Total</span><span class="value"> RM '.$subtotal.' </span></li>
      <li class="totalRow"><a href="#" class="btn-trans continue ';
      if (($disable == 0) && ($_SESSION["subtotal"] != 0)) {
        // if the $disable value is false then we will only add the class that will trigger the confirm transaction process //
        echo "checkout_trans";
      }
      echo
      '">Checkout</a></li>
    </ul>
  </div>
</div>';
?>
