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

if (empty($_SESSION["brand_id"])) {
  $_SESSION["brand_id"] = $_GET["brandid"];
}

// now defining the brand_id and creating an order_id depends on the situation //
// the number of page table that we will be receiving //
if (($_SESSION['brand_id'] != $_GET["brandid"]) && (($_SESSION['order_id']) != "")) {
  // if the cashier entered the other brand the order id will that hasn't being paid/concluded will be deleted, and the related $_SESSION will be cleared back to empty string as well //
  // begin the query of deletion of the related order, and clearing the $_SESSION back to empty string, then replace them with the new value //
  // but first we have to update the Foreign Key of the related table to NULL in order to delete the order due to SYNTAX //
  $UPDATENULL = "UPDATE user_order SET user_id = NULL, brand_id = NULL, cashier_id = NULL WHERE order_id = '".$_SESSION['order_id']."'";
  $UPDATENULLQ = mysqli_query($con, $UPDATENULL);
  if (mysqli_affected_rows($con) < 1) {
    // if there's no result then inform the user and show the possible SQL error //
    echo "<script>alert('WARNING: Order ID can\'t be updated, you will be return back to Cashier Panel. Possible Error: ".mysqli_error($con)."');";
    echo "window.location.href='cashier_panel.html';</script>";
  } else {
    // if we're able to update the Foreign Key, proceeds to another related table to update their Foreign Key as well //
    echo "<script>alert('Notice: Order ID: ".$_SESSION['order_id']." updated, now updating another table\'s Foreign Key in order to proceed the deletion...');</script>";
    // then we've to first get the transaction_id from the related records in transaction_record table so in latter process we will able to delete the record specifically //
    // the variable will be an array to hold all the transaction_id //
    $transaction_id = "";
    // defining and execute the query which finds all the related transaction records //
    $TRANSID = "SELECT * FROM transaction_record WHERE order_id = '".$_SESSION['order_id']."'";
    $TRANSIDQ = mysqli_query($con, $TRANSID);
    if (mysqli_num_rows($TRANSIDQ) < 1) {
      // if we're able to retrieve the transaction_id, alert the user about it //
      echo "<script>alert('WARNING: Unable to retrieve transaction_records under Order ID: ".$_SESSION['order_id'].", or there\'s no record existing in the transaction record table. Checking the types of error occured...');</script>";
      // if there's no record under transaction_record table, then we can straight delete the order_id //
      $DELETEORD = "DELETE FROM user_order WHERE paid < 1 AND order_id = '".$_SESSION['order_id']."'";
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

        $_SESSION['cart_numb'] = "0";
        echo "<script>alert('Notice: Deletion Process completed. Continue to proceed your intended progress.');";
        echo "window.location.href='cashier_panel.html';</script>";
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
      $UPDATENULL = "UPDATE transaction_record SET meal_id = NULL, meal_brand_id = NULL, order_id = NULL WHERE order_id = '".$_SESSION['order_id']."'";
      $UPDATENULLQ = mysqli_query($con, $UPDATENULL);
      if (mysqli_affected_rows($con) < 1) {
        // if there's no result then inform the user and show the possible SQL error //
        echo "<script>alert('WARNING: Transaction Record ID can\'t be updated, you will be return back to Cashier Panel. Possible Error: ".mysqli_error($con)."');";
        echo "window.location.href='cashier_panel.html';</script>";
      } else {
        // if we've updated the table, begin the process of deletion //
        echo "<script>alert('Notice: Transaction Record ID updated, now begin the deletion of the related information.');</script>";
        $DELETEORD = "DELETE FROM user_order WHERE paid < 1 AND order_id = '".$_SESSION['order_id']."'";
        $DELETEORDQ = mysqli_query($con, $DELETEORD);
        if (mysqli_affected_rows($con) < 1) {
          // if there's no result then inform the user and show the possible SQL error //
          echo "<script>alert('WARNING: Order ID: ".$_SESSION['order_id']." can\'t be deleted, you will be return back to Cashier Panel. Possible Error: ".mysqli_error($con)."');";
          echo "window.location.href='cashier_panel.html';</script>";
        } else {
          // if we've deleted the related info in order table, begin the final deletion on transaction_record table //
          echo "<script>alert('Notice: Order ID: ".$_SESSION['order_id']." deleted, now begin the deletion of the related information.');</script>";
          while (list($key,$val) = @each ($transaction_id)) {
            $DELETETRANS = "DELETE FROM transaction_record WHERE transaction_id = $val";
            $DELETETRANSQ = mysqli_query($con, $DELETETRANS);
            if (mysqli_affected_rows($con) < 1) {
              // if we are unable to delete the transaction record, something must gone wrong //
              echo "<script>alert('WARNING: Unable to delete the Transaction ID: ".$val.". Please try again. Possible Error: ".mysqli_error($con)."');";
              echo "</script>";
            } else {
              echo "<script>alert('Notice: Deleted Transaction ID: ".$val.".');";
              echo "</script>";
            }
          }
          // if we have reached this point then deletion process is completed, and begin to revert the $_SESSION back to empty string //
          $_SESSION['brand_id'] = "";
          $_SESSION['order_id'] = "";
          $_SESSION['buyer_id'] = "";

          $_SESSION['cart_numb'] = "0";
          echo "<script>alert('Notice: Deletion Process completed. Continue to proceed your intended progress.');";
          echo "window.location.href='cashier_panel.html';</script>";
        }
      }
    }
  }
  // at the end, assign the new value into $_SESSION, and create a new order for the new $_SESSION //
  $_SESSION['brand_id'] = $_GET["brandid"];
  $_SESSION['cart_numb'] = "0";
} else {
  // if there's no value then return the user back to cashier panel //
  echo "<script>alert('Notice: You still yet to define the Brand ID, ".$_SESSION['role']." ".$_SESSION['last_name'].". You will be return back to Cashier Panel.');";
  echo "window.location.href='cashier_panel.html';</script>";
}

// now defining the scenario might occured //
if ((isset($_SESSION['brand_id'])) && (($_SESSION['brand_id']) !== "")) {
  // if there's a brand_id then we check our session possessed an order_id or not //
  if ((isset($_SESSION['order_id'])) && (($_SESSION['order_id']) !== "")) {
    // if there's an order_id then we check the brand_id is the same as $_SESSION["brand_id"] or not since we've to only allow the order can be made specifically by the meal shop/brand //
    $CHECKBRAND = "SELECT * FROM user_order WHERE brand_id = '".$_SESSION['brand_id']."' AND order_id = '".$_SESSION['order_id']."' AND paid = '0'";
    $CHECKBRANDQ = mysqli_query($con, $CHECKBRAND);
    if (mysqli_num_rows($CHECKBRANDQ) < 1) {
      // if the order can't be found it might be paid, or the order simplly doesn't exists, so we will have to clear the $_SESSION for the order id, and create a new one and retrieve it's primary key to the $_SESSION at the same time //
      // inform the cashier first //
      echo "<script>alert('Notice: Order ID can\'t be found or already paid, ".$_SESSION['role']." ".$_SESSION['last_name'].". Creating a new order now...');</script>";
      // first create an order_id in the database, then retrieve it's primary key //
      $INSERTORDER = "INSERT INTO user_order
                      (`paid`, `brand_id`, `cashier_id`)
                      VALUES (0, '".$_SESSION['brand_id']."', '".$_SESSION['user_id']."')";
      $INSERTORDERQ = mysqli_query($con, $INSERTORDER);
      if (mysqli_affected_rows($con) < 1) {
        // if there's no result then inform the user and show the possible SQL error //
        echo "<script>alert('WARNING: Order ID can\'t be generated, you will be return back to Cashier Panel. Possible Error: ".mysqli_error($con)."');";
        echo "window.location.href='cashier_panel.html';</script>";
      } else {
        // if we're able to retrieve the primary key then assign it into the $_SESSION variable //
        $_SESSION["order_id"] = mysqli_insert_id($con);
        $_SESSION['cart_numb'] = "0";
        echo "<script>alert('Notice: Order ID: ".$_SESSION['order_id']." generated.');</script>";
      }
    } else {
      // if there's one then inform the cashier it's verified and do nothing //
      echo "<script>alert('Notice: Order ID verified.');</script>";
    }
  } else {
    // if there's no order_id existing within the $_SESSION then we wil create one and retrieve it's primary key into the $_SESSION //
    // first create an order_id in the database, then retrieve it's primary key //
    $INSERTORDER = "INSERT INTO user_order
                    (`paid`, `brand_id`, `cashier_id`)
                    VALUES (0, '".$_SESSION['brand_id']."', '".$_SESSION['user_id']."')";
    $INSERTORDERQ = mysqli_query($con, $INSERTORDER);
    if (mysqli_affected_rows($con) < 1) {
      // if there's no result then inform the user and show the possible SQL error //
      echo "<script>alert('WARNING: Order ID can\'t be generated, you will be return back to Cashier Panel. Possible Error: ".mysqli_error($con)."');";
      echo "window.location.href='cashier_panel.html';</script>";
    } else {
      // if we're able to retrieve the primary key then assign it into the $_SESSION variable //
      $_SESSION["order_id"] = mysqli_insert_id($con);
      $_SESSION['cart_numb'] = "0";
      echo "<script>alert('Notice: Order ID: ".$_SESSION['order_id']." generated.');</script>";
    }
  }
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
    <!-- MainMenu-Area-End -->

    <header class="site-header">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 text-center">
                	<img src="image/logo_symbol.png" alt="logo" />
                    <h1 class="white-color">Cashier Panel: Meal List</h1>
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
                  <div class="separator clear-left">
                      <p class="btn-add">
                          <i class="fa fa-shopping-cart"></i><a class="hidden-sm" onclick="';
                          // if the meal out of stock then don't allow the user to click to add to cart: not setting the function() to get the meal_id's value //
                          if ($row["meal_quantity"] != 0) {
                            // if its not 0 then only include the add to cart function here //
                            echo "mealcartList(this)";
                          }
                          echo
                          '" value="';
                          // if the meal out of stock then don't allow the user to click to add to cart: not setting the value that allow the system get the meal_id //
                          if ($row["meal_quantity"] != 0) {
                            // if its not 0 then only include the add to cart function here //
                            echo $row["meal_id"];
                          }
                          echo
                          '">Add to Cart</a></p>
                      <p class="btn-details">
                          <i class="fa fa-list"></i><a class="hidden-sm" value="'.$row["meal_id"].'" onclick="mealContent(this)">More Details</a></p>
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
