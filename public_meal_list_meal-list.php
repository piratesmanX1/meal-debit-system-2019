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
// destroy $_SESSION ["meal_id"] just incase when cashier return from meal_content page caused some disruption to the system //
if ((isset($_SESSION["meal_id"])) && ($_SESSION["meal_id"] != "")) {
  unset($_SESSION["meal_id"]);
}

// now defining the brand_id and creating an order_id depends on the situation //
// the number of page table that we will be receiving //
if ((isset($_GET["brandid"]))) {
  // now define the value to a variable, if the brandid delievered to the other side AND if the $_SESSION is empty as well //
  $_SESSION['brand_id'] = $_GET["brandid"];
} else {
  // if there's no value then return the user back to cashier panel //
  echo "<script>alert('Notice: You still yet to define the Brand ID.');";
  echo "window.location.href='public_meal_list.html';</script>";
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
                    <li><a href="#" onclick="branList()">Trending</a></li>
                    <li><a href="#" onclick="branList()">Brands</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- MainMenu-Area-End -->

    <header class="site-header">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 text-center">
                	<img src="image/logo_symbol.png" alt="logo" />
                    <h1 class="white-color"> Meal List </h1>
                    <ul class="breadcrumb">
                        <li> Meal-Debit System </li>
                    </ul>
                </div>
            </div>
        </div>
    </header><div class="section-padding">
        <div class="container">
            <div class="row" id="trending">';
?>

<?php
// begin to call out the content of the related brand //
// Formula: The Brand with the highest Meal sold //
$RELBRAN = "SELECT meal.meal_brand_id, meal_brand.brand_name, meal_brand.registered_date, meal_brand.brand_image,
             SUM((meal.meal_additional_quantity + meal.meal_default_quantity) - meal.meal_quantity) AS BRAND_MEAL_SOLD,
             SUM(meal.meal_additional_quantity + meal.meal_default_quantity) AS BRAND_MEAL_TOTAL
             FROM meal INNER JOIN meal_brand
             ON meal.meal_brand_id = meal_brand.brand_id
             WHERE meal_brand.brand_id = '".$_SESSION["brand_id"]."'
             GROUP BY meal.meal_brand_id";
$RELBRANQ = $con->query($RELBRAN);
if (mysqli_num_rows($RELBRANQ) < 1) {
  $brand_image = "/APU/SDP/image/e1.png";
  $brand_name = " - ";
  $brand_meal_sold = " - ";
  $brand_meal_total = ' - ';
  $brand_registered_date = " - ";
  $brandpercent = " - ";
} else {
  if ($row = mysqli_fetch_array($RELBRANQ)) {
    if (isset($row["brand_image"])) {
      $brand_image = $row["brand_image"];
    } else {
      $brand_image = "/APU/SDP/image/e3.png";
    }
    $brand_name = $row["brand_name"];
    $brand_meal_sold = $row["BRAND_MEAL_SOLD"];
    $brand_meal_total = $row["BRAND_MEAL_TOTAL"];
    // Convert into 12AM/PM format //
    $register_date = $row['registered_date'];
    $brand_registered_date = date('d/m/y h:i A', strtotime($register_date));
    // Conver into 2 decimal for percentage //
    $brandpercent = number_format((float)((($row["BRAND_MEAL_SOLD"]) / ($row["BRAND_MEAL_TOTAL"])) * 100), 2, '.', '');
  }
}
echo '
                <div class="col-xs-12">
                    <article class="post-single sticky">
                        <figure class="post-media">
                            <img style="height:250px;width:400px;" src="'.$brand_image.'" alt="top-brand">
                        </figure>
                        <div class="post-body">
                            <div class="post-meta">
                                <div class="post-tags"><a style="color:red!important"><i class="fa fa-list-alt" aria-hidden="true"></i> MEAL\'S BRAND </a></div>
                                <div class="post-date"> '.$brand_registered_date.' </div>
                            </div>
                            <h4 class="dark-color"><a>'.$brand_name.'</a></h4>
                            <p>Meal Sold: '.$brand_meal_sold.' </p>
                            <p>Meal Total Quantity: '.$brand_meal_total.' </p>
                            <p>Overall Sales Rate: '.$brandpercent.' % </p>
                        </div>
                    </article>
                    <div class="space-100"></div>
                </div>
              </div>
';
?>

<?php
// now to find the number of records //
// before we start to call the content of the meal we will check whether there's active meal existing within the table //
$CHECKMEAL = "SELECT * FROM meal WHERE meal_brand_id = '".$_SESSION["brand_id"]."' AND active = 1";
$CHECKMEALQ = mysqli_query($con, $CHECKMEAL);
if (mysqli_num_rows($CHECKMEALQ) < 1) {
  // if there's none then inform the user about it //
  $meal_record = 0;
} else {
  // if there is then we begin to call the content of the meal list //
  // begin to call the meal values depends on the $_SESSION //
  $MEALLIST = "SELECT *
               FROM meal INNER JOIN meal_brand
               ON meal.meal_brand_id = meal_brand.brand_id
               WHERE meal_brand.brand_id = '".$_SESSION["brand_id"]."'
               GROUP BY meal.meal_id";
  $result = $con->query($MEALLIST);
  if ($result->num_rows > 0) {
    // we define the number of records we will get into the variables since we will seperate the portions of the meal list. Example: 1 row can only contain 4 records, if it's over 4 then we will have to generate out the extra HTML variable to seperate it //
    $cartrow = 0;
    $itemnumb = 0;
    // we now call out the HTML attribute for the first loop first //
    while ($row = $result->fetch_assoc()) {
      $cartrow ++;
      if ($cartrow == 8) {
        // then if we've reached total of 2 rows per item (a.k.a 8 records per item) then we call out another specific HTML attribute to seperate it //
        // then we reset the $cartrow back to 0 //
        $cartrow = 0;
        // and make a record that the we've made a new "page" //
        $itemnumb++;
      }
    }
    $meal_record = (($itemnumb * 8) + $cartrow);
  }
}
?>

<?php
echo '
<div class="container">
    <div class="row">
        <div class="row">
            <div class="col-md-9">
                <h3> '.$brand_name.'\'s Meal List</h3>
                <h5> <span id="meal-records">'.$meal_record.'</span> types of meal record(s) available</h5>
            </div>
            <div class="col-md-3">
                <!-- Controls -->
                <div class="controls pull-right hidden-xs">
                    <a class="left fa fa-chevron-left btn btn-success" href="#carousel-example"
                        data-slide="prev"></a><a class="right fa fa-chevron-right btn btn-success" href="#carousel-example"
                            data-slide="next"></a>
                </div>
            </div>
        </div>
        <div id="carousel-example" class="carousel slide hidden-xs" data-ride="carousel">
            <!-- Wrapper for slides -->
            <div class="carousel-inner">
            <!-- 1 items per "page" -->';

// before we start to call the content of the meal we will check whether there's active meal existing within the table //
$CHECKMEAL = "SELECT * FROM meal WHERE meal_brand_id = '".$_SESSION["brand_id"]."' AND active = 1";
$CHECKMEALQ = mysqli_query($con, $CHECKMEAL);
if (mysqli_num_rows($CHECKMEALQ) < 1) {
  // if there's none then inform the user about it //
  echo '<center><span style="color:red!important">Notice: There\'s no active meal record existing within the table. </span></center>';
  $meal_record = 0;
} else {
  // if there is then we begin to call the content of the meal list //
  // begin to call the meal values depends on the $_SESSION //
  $MEALLIST = "SELECT *
               FROM meal INNER JOIN meal_brand
               ON meal.meal_brand_id = meal_brand.brand_id
               WHERE meal_brand.brand_id = '".$_SESSION["brand_id"]."'
               GROUP BY meal.meal_id";
  $result = $con->query($MEALLIST);
  if ($result->num_rows > 0) {
    // we define the number of records we will get into the variables since we will seperate the portions of the meal list. Example: 1 row can only contain 4 records, if it's over 4 then we will have to generate out the extra HTML variable to seperate it //
    $cartrow = 0;
    $itemnumb = 0;
    // we now call out the HTML attribute for the first loop first //
    echo '
    <div class="item active">
    <!-- 2 rows per item -->
      <div class="row" style="padding-bottom:10px!important">
      <!-- 4 meals per row -->
    ';
    while ($row = $result->fetch_assoc()) {
      if (isset($row["meal_image"])) {
        $meal_image = $row["meal_image"];
      } else {
        $meal_image = "/APU/SDP/image/e3.png";
      }
      echo '
      <div class="col-sm-3 meal-list">
          <div class="col-item">
              <div class="photo">
                  <img src="'.$meal_image.'" style="width:350px!important;height:260px!important" class="img-responsive" alt="a" />
              </div>
              <div class="info">
                  <div class="row meal-content">
                      <div class="price col-md-6">
                          <h5 class="meal-name">'.$row["meal_name"].'</h5>
                          <h5 class="price-text-color"> RM '.$row["meal_price"].' </h5>
                      </div>
                      <div class="rating hidden-sm col-md-6">
                          <span>';
                          // now define whether there is stock available for the meal or not //
                          if ($row["meal_quantity"] == 0) {
                            echo "<span style='color:red!important;'> Out of Stock <span>";
                          } else {
                            echo "Stock: ".$row['meal_quantity'];
                          }
                          echo
                          '</span>
                      </div>
                  </div>
                  <div class="clearfix">
                  </div>
              </div>
          </div>
      </div>
      ';
      // then everytime it goes per turn it will increase by 1 //
      $cartrow ++;
      if ($cartrow == 4) {
        //if it reached the maximum limit of the row (4) then we echo out the HTML attribute to seperate it //
        echo '
        </div>
        <div class="row" style="padding-bottom:10px!important">
        <!-- 4 meals per row -->
        ';
      } else if ($cartrow == 8) {
        // then if we've reached total of 2 rows per item (a.k.a 8 records per item) then we call out another specific HTML attribute to seperate it //
        echo '
          </div>
        </div>
        <div class="item active">
        <!-- 2 rows per item -->
          <div class="row" style="padding-bottom:10px!important">
          <!-- 4 meals per row -->
        ';
        // then we reset the $cartrow back to 0 //
        $cartrow = 0;
        // and make a record that the we've made a new "page" //
        $itemnumb++;
      }
    }
    $meal_record = (($itemnumb * 8) + $cartrow);
    echo '
      </div>
    </div>
    ';
  }
}

echo '
            </div>
        </div>
    </div>
</div>

<script type="text/javascript"></script>
';
?>
