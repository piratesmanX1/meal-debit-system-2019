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
// the value of the meal_id we will receiving //
if ((isset($_GET["mealid"])) || ($_GET["mealid"] != "")) {
  $meal_id = $_GET["mealid"];
  // define it in $_SESSION as well to perform the function //
  $_SESSION["meal_id"] = $meal_id;
}
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
<nav class="mainmenu-area affix" style="position: relative;!important" data-spy="affix" data-offset-top="200">
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
                        <a class="logout" href="logout_cashier.php">LOG OUT <i class="fas fa-sign-out-alt"></i></a>
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
<!-- Meal Content Style and Script -->
<link href="vendor/product-page/product-page/style.css" rel="stylesheet">
<script src="vendor/product-page/product-page/script.js" charset="utf-8"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js" charset="utf-8"></script>

<main class="container-meal-content" style="font-family:'Roboto', sans-serif!important;">

<?php
// begin to take in the contents of the meal based on the meal id //
if ((isset($meal_id)) && ($meal_id != "")) {
  $MEALCONT = "SELECT *
               FROM meal INNER JOIN meal_brand
               ON meal.meal_brand_id = meal_brand.brand_id
               WHERE meal.meal_id = '".$meal_id."' AND meal.active = 1";
  $MEALCONTQ = $con->query($MEALCONT);

  if (mysqli_num_rows($MEALCONTQ) < 1) {
    $meal_image = "/APU/SDP/image/e1.png";
    echo '
          <!-- Left Column / Headphones Image -->
          <div class="left-column">
            <img data-image="red" class="active" style="width:700px!important;height:630px!important;" src="'.$meal_image.'" alt="meal_image">
          </div>

          <!-- Right Column -->
          <div class="right-column">
            <!-- Product Description -->
            <div class="product-description">
              <span> - </span>
              <h1> - </h1>
              <p> Content can\'t be found. </p>
            </div>

            <!-- Product Configuration -->
            <div class="product-configuration">
              <!-- Product Color -->
              <div class="product-color">
                <span>Quantity</span>
                <div class="color-choose">
                  <input type="number" step="1" min="1" disabled value="0" max="">
                </div>
              </div>
              <!-- Some notice here -->
              <div class="cable-config">
                <a>Having questions with our meal?</a>
              </div>
            </div>

            <!-- Product Pricing -->
            <div class="product-price">
              <span> RM - </span>
              <a class="cart-btn">Add Cart</a>
              <a class="cart-btn" onclick="mealBack(this)" style="margin:20px!important;background-color:red!important;">Return</a>
            </div>
          </div>';
  } else {
    if ($row = mysqli_fetch_array($MEALCONTQ)) {
      if (isset($row["meal_image"])) {
        $meal_image = $row["meal_image"];
      } else {
        $meal_image = "/APU/SDP/image/e3.png";
      }
    echo '
          <!-- Left Column / Headphones Image -->
          <div class="left-column">
            <img data-image="red" class="active" style="width:700px!important;height:630px!important;" src="'.$meal_image.'" alt="meal_image">
          </div>

          <!-- Right Column -->
          <div class="right-column">
            <!-- Product Description -->
            <div class="product-description">
              <span> '.$row["brand_name"].' </span>
              <h1> '.$row["meal_name"].' </h1>
              <p>'.$row["meal_details"].'</p>
            </div>

            <!-- Product Configuration -->
            <div class="product-configuration">
              <!-- Product Color -->
              <div class="product-color">
                <span>Quantity</span>
                <div class="color-choose">
                  <input type="number" ';
                  if ($row["meal_quantity"] == 0) {
                    echo "disabled";
                  }
                  echo
                  ' id="meal-quantity-content" step="1" min="1" onKeyDown="return false" max="'.$row["meal_quantity"].'">
                </div>
              </div>
              <!-- Some notice here -->
              <div class="cable-config">
                <a>Having questions with our meal?</a>
              </div>
            </div>

            <!-- Product Pricing -->
            <div class="product-price">
              <span> RM '.$row["meal_price"].' </span>
              <a class="cart-btn" id="';
              if ($row["meal_quantity"] != 0) {
                echo "add_cart_content";
              }
              echo
              '">Add Cart</a>
              <a class="cart-btn" onclick="mealBack(this)" style="margin:20px!important;background-color:red!important;" value="'.$row["brand_id"].'">Return</a>
            </div>
          </div>';
    }
  }
}
?>

    </main>
