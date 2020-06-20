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
// the number of page table that we will be receiving //
if (isset($_GET["page"])) {
  $page = $_GET["page"];
} else {
  $page = "1";
}
// if there's no input data then we will treat the variable as 0, as there's no indication of extra page needed //
if (($page == "") || $page == "1") {
  $page_count = 0;
} else {
  // we will multiply the input data by 6, which will result changes in SQL query like "LIMIT 10, 6" or "LIMIT 20, 6" //
  $page_count = (($page - 1) * 6);
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
                    <li><a href="public_meal_list.html#trending">Trending</a></li>
                    <li><a href="public_meal_list.html#brand">Brands</a></li>
                </ul>';

                echo '
            </div>
        </div>
    </nav>
    <!-- MainMenu-Area-End -->

    <header class="site-header">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 text-center">
                	<img src="image/logo_symbol.png" alt="logo" />
                    <h1 class="white-color"> Brand List </h1>
                    <ul class="breadcrumb">
                        <li> Meal-Debit System </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <div class="section-padding">
        <div class="container">
            <div class="row" id="trending">';
?>

<?php
// begin to call out the trending brand //
// Formula: The Brand with the highest Meal sold //
$TRENDING = "SELECT meal.meal_brand_id, meal_brand.brand_name, meal_brand.registered_date, meal_brand.brand_image,
             SUM((meal.meal_additional_quantity + meal.meal_default_quantity) - meal.meal_quantity) AS BRAND_MEAL_SOLD,
             SUM(meal.meal_additional_quantity + meal.meal_default_quantity) AS BRAND_MEAL_TOTAL
             FROM meal INNER JOIN meal_brand
             ON meal.meal_brand_id = meal_brand.brand_id
             GROUP BY meal.meal_brand_id
             ORDER BY BRAND_MEAL_SOLD DESC
             LIMIT 1";
$TRENDINGQ = $con->query($TRENDING);
if (mysqli_num_rows($TRENDINGQ) < 1) {
  $top_brand_image = "/APU/SDP/image/e1.png";
  $top_brand_name = " - ";
  $top_brand_meal_sold = " - ";
  $top_brand_meal_total = ' - ';
  $top_brand_registered_date = " - ";
  $brandpercent = " - ";
} else {
  if ($row = mysqli_fetch_array($TRENDINGQ)) {
    if (isset($row["brand_image"])) {
      $top_brand_image = $row["brand_image"];
    } else {
      $top_brand_image = "/APU/SDP/image/e3.png";
    }
    $top_brand_name = $row["brand_name"];
    $top_brand_meal_sold = $row["BRAND_MEAL_SOLD"];
    $top_brand_meal_total = $row["BRAND_MEAL_TOTAL"];
    // Convert into 12AM/PM format //
    $register_date = $row['registered_date'];
    $top_brand_registered_date = date('d/m/y h:i A', strtotime($register_date));
    // Conver into 2 decimal for percentage //
    $brandpercent = number_format((float)((($row["BRAND_MEAL_SOLD"]) / ($row["BRAND_MEAL_TOTAL"])) * 100), 2, '.', '');
  }
}
echo '
                <div class="col-xs-12">
                    <article class="post-single sticky">
                        <figure class="post-media">
                            <img style="height:250px;width:400px;" src="'.$top_brand_image.'" alt="top-brand">
                        </figure>
                        <div class="post-body">
                            <div class="post-meta">
                                <div class="post-tags"><a style="color:red!important"><i class="fas fa-fire"></i> TRENDING BRAND </a></div>
                                <div class="post-date"> '.$top_brand_registered_date.' </div>
                            </div>
                            <h4 class="dark-color"><a>'.$top_brand_name.'</a></h4>
                            <p>Meal Sold: '.$top_brand_meal_sold.' </p>
                            <p>Meal Total Quantity: '.$top_brand_meal_total.' </p>
                            <p>Overall Sales Rate: '.$brandpercent.' % </p>
                        </div>
                    </article>
                    <div class="space-100"></div>
                </div>
';
?>

<?php
echo '
            </div>
            <div class="row" id="brand">
';

// begin to call out the brands, 6 per page //
$BRANDA = "SELECT * FROM meal_brand
           WHERE active = 1
           LIMIT $page_count,6";
$result = $con->query($BRANDA);
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    // now convert the datetime value to 12AM/PM format //
    $register_date = $row['registered_date'];
    $register_date = date('d/m/y h:i A', strtotime($register_date));
    // now define the path of the image //
    if (isset($row["brand_image"])) {
      $brand_image = $row["brand_image"];
    } else {
      $brand_image = "/APU/SDP/image/e3.png";
    }

echo '
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <article class="post-single">
                        <figure class="post-media">
                            <img style="height:175px;width:300px;" src="'.$brand_image.'" alt="brand_image">
                        </figure>
                        <div class="post-body">
                            <div class="post-meta">
                                <div class="post-tags"><a href="#brand"> BRAND </a></div>
                                <div class="post-date">'.$register_date.'</div>
                            </div>
                            <h4 class="dark-color"><a>'.$row["brand_name"].'</a></h4>
                            <a class="read-more" style="cursor:pointer;" onclick="mealList(this);" value="'.$row["brand_id"].'">Go to Meal List</a>
                        </div>
                    </article>
                </div>';
  }
}

echo '
            </div>
';

// creating the page number based on the list's number, if its over 6 records then create 1 page //
// we first find how many records in the list //
$TOTALBRAN = "SELECT COUNT(*) AS TOTAL_BRAND
              FROM meal_brand
              WHERE active = 1";
$TOTALBRANQ = mysqli_query($con, $TOTALBRAN);
if (mysqli_num_rows($TOTALBRANQ) < 1) {
  // only 1 page needed //
  $page_numb = "1";
} else {
  // count how many pages are needed //
  if ($row = mysqli_fetch_array($TOTALBRANQ)) {
    $total_rep = $row["TOTAL_BRAND"];
    // then we divide the number of total record by 6, then round up, i.e. 1.1 > 2; since any extra records will required 1 new page //
    $page_numb = ($total_rep / 6);
    $page_numb = ceil($page_numb);
  }
}

// now creating the page numbers //
echo '
            <div class="row">
                <div class="col-xs-12">
                    <div class="pagination">
                        <div class="nav-links">
                            <a class="prev page-numbers" onclick="branTable(this)" value="';
                            // if the current $_GET['page'] number is not more than 2 then it has no value, but 1 //
                            if ($page < 2) {
                              $previous_page = 1;
                              echo $previous_page;
                            } else {
                              $previous_page = ($page - 1);
                              echo ($page - 1);
                            }
                            echo '" id="page-'.$previous_page.'"';

                       echo '"><i class="lnr lnr-chevron-left"></i></a>';

                      echo '<a class="page-numbers';
                      // highlight the current page number if the $_GET["page"] is the current page //
                      if ($page == 1) {
                        echo ' current';
                      }
                      echo
                      '" onclick="branTable(this)" id="page-1" value="1" >1</a>';
                      for ($n = 2; $n <= $page_numb; $n++) {
                        echo '<a class="page-numbers';
                        // highlight the current page number if the $_GET["page"] is the current page //
                        if ($page == $n) {
                          echo ' current';
                        }
                        echo '" onclick="branTable(this)" id="page-'.$n.'" value="'.$n.'">'.$n.'</a>';
                      }
                      echo'
                            <a class="next page-numbers" onclick="branTable(this)" value="';
                            // By default $next_page if no value and the total records division is no more than 1 then the value of it is 1 //
                            if (empty($next_page) && ($page_numb > 1)) {
                              $next_page = 2;
                            } else if (empty($next_page) && ($page_numb < 2)) {
                              $next_page = 1;
                            }
                            // if the total records division is not more than 2, then it has no value, but 1 //
                            if ($page_numb < 2) {
                              $next_page = 1;
                              echo $next_page;
                            } else {
                              if ($page == $page_numb) {
                                // if the current $_GET['page'] reached its limit then we will make the value the maximum one //
                                $next_page = $page;
                                echo $next_page;
                              } else {
                                $next_page = ($page + 1);
                                echo ($page + 1);
                              }
                            }
                      echo  '" id="page-'.$next_page.'"><i class="lnr lnr-chevron-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>';

echo '
        </div>
    </div>
';
?>
