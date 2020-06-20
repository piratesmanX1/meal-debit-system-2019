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
// declaring essential variables and SQL Queries to pull out the informations //
  // Present Date //
  $present = date_create();
  // Taking the current time //
  date_default_timezone_set("Etc/GMT-8");
  $now = date("Y-m-d H:i:s");
  // Name of the current month //
  $month_name = (strftime("%B",time()));
  // Variable to store Error Message //
  $error = '';

  // Check there's how many user registered in this month at the current year //
  $USERREG = "SELECT COUNT(*) AS USERM FROM user WHERE MONTH(registered_date) = MONTH(CURRENT_TIMESTAMP()) AND YEAR(registered_date) = YEAR(CURRENT_TIMESTAMP())";
  $USERREGQ = mysqli_query($con, $USERREG);
  // Check the total number of the registered user //
  $USERTOTAL = "SELECT COUNT(*) AS USER FROM user";
  $USERTOTALQ = mysqli_query($con, $USERTOTAL);
  // Check the number of students registered this month //
  $STUDENTREG = "SELECT COUNT(*) AS STUDENTM FROM user WHERE MONTH(registered_date) = MONTH(CURRENT_TIMESTAMP()) AND YEAR(registered_date) = YEAR(CURRENT_TIMESTAMP()) AND status = 0";
  $STUDENTREGQ = mysqli_query($con, $STUDENTREG);
  // Check the total number of students //
  $STUDENTTOTAL = "SELECT COUNT(*) AS STUDENT FROM user WHERE status = 0";
  $STUDENTTOTALQ = mysqli_query($con, $STUDENTTOTAL);
  // Check the number of cashiers registered this month //
  $CASHIERREG = "SELECT COUNT(*) AS CASHIERM FROM user WHERE MONTH(registered_date) = MONTH(CURRENT_TIMESTAMP()) AND YEAR(registered_date) = YEAR(CURRENT_TIMESTAMP()) AND status = 1";
  $CASHIERREGQ = mysqli_query($con, $CASHIERREG);
  // Check the total number of cashiers //
  $CASHIERTOTAL = "SELECT COUNT(*) AS CASHIER FROM user WHERE status = 1";
  $CASHIERTOTALQ = mysqli_query($con, $CASHIERTOTAL);
  // Check the number of admins registered this month //
  $ADMINREG = "SELECT COUNT(*) AS ADMINM FROM user WHERE MONTH(registered_date) = MONTH(CURRENT_TIMESTAMP()) AND YEAR(registered_date) = YEAR(CURRENT_TIMESTAMP()) AND status = 2";
  $ADMINREGQ = mysqli_query($con, $ADMINREG);
  // Check the total number of admins //
  $ADMINTOTAL = "SELECT COUNT(*) AS ADMIN FROM user WHERE status = 2";
  $ADMINTOTALQ = mysqli_query($con, $ADMINTOTAL);
  // Check which brand having the highest sales at the current month //
  // Formula: //
  // Check each specific product's sales: Default Quantity + Additional Quantity - Meal Quantity //
  // Then calculate everything with this formula, and combine. Label specifically based on the brand. //
  $HIGHSALE = "SELECT
               meal_brand_id, SUM((meal_additional_quantity + meal_default_quantity) - meal_quantity)
               AS BRAND_SALES
               FROM meal
               WHERE active > 0
               GROUP BY meal_brand_id
               ORDER BY BRAND_SALES DESC LIMIT 1";
  $HIGHSALEQ = mysqli_query($con, $HIGHSALE);
  // Show the Top 10 meals that being sold for the month //
  $TOP10MEAL = "SELECT
               meal_id, meal_name,
               ((meal_additional_quantity + meal_default_quantity) - meal_quantity)
               AS MEAL_SOLD
               FROM meal
               WHERE active > 0
               GROUP BY meal_id
               ORDER BY MEAL_SOLD DESC LIMIT 10";
  // Calculate the total number of meal being sold //
  $TOTALMEAL = "SELECT
               SUM((meal_additional_quantity + meal_default_quantity) - meal_quantity)
               AS TOTAL_MEAL_SOLD
               FROM meal
               WHERE active > 0";
  $TOTALMEALQ = mysqli_query($con, $TOTALMEAL);
?>

<?php
  echo     '<!-- top tiles -->
           <div class="row tile_count">
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-user"></i> Total Users </span>
              <div class="count">';
              // show the number of total user //
              if (mysqli_num_rows($USERTOTALQ) < 1) {
                  echo "-";
              } else {
                if ($row = mysqli_fetch_array($USERTOTALQ)) {
                  // putting commas into the digits //
                  echo number_format($row["USER"]);
                }
              }
              echo '</div>
              <span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i>';
              // show the number of user registered this month //
              if (mysqli_num_rows($USERREGQ) < 1) {
                  echo "-";
              } else {
                if ($row = mysqli_fetch_array($USERREGQ)) {
                  // store the info into variable so we will able to perform calculation later //
                  $totaluser = $row["USERM"];
                  // store the info into variable so we will able to perform calculation later //
                  $userm = $row["USERM"];
                  // putting commas into the digits //
                  echo number_format($row["USERM"]);
                }
              }
              echo '</i> users this month</span>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fas fa-user-graduate"></i> Total Students </span>
              <div class="count">';
              // show the number of total students //
              if (mysqli_num_rows($STUDENTTOTALQ) < 1) {
                  echo "-";
              } else {
                if ($row = mysqli_fetch_array($STUDENTTOTALQ)) {
                  // putting commas into the digits //
                  echo number_format($row["STUDENT"]);
                }
              }
              echo '</div>
              <span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i>';
              // show the number of student registered this month //
              if (mysqli_num_rows($STUDENTREGQ) < 1) {
                  echo "-";
              } else {
                if ($row = mysqli_fetch_array($STUDENTREGQ)) {
                  // store the info into variable so we will able to perform calculation later //
                  $studentm = $row["STUDENTM"];
                  // putting commas into the digits //
                  echo number_format($row["STUDENTM"]);
                }
              }
              echo '</i> students this month</span>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="far fa-address-card"></i> Total Cashiers </span>
              <div class="count">';
              // show the number of total cashier //
              if (mysqli_num_rows($CASHIERTOTALQ) < 1) {
                  echo "-";
              } else {
                if ($row = mysqli_fetch_array($CASHIERTOTALQ)) {
                  // putting commas into the digits //
                  echo number_format($row["CASHIER"]);
                }
              }
              echo '</div>
              <span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i>';
              // show the number of cashier registered this month //
              if (mysqli_num_rows($CASHIERREGQ) < 1) {
                  echo "-";
              } else {
                if ($row = mysqli_fetch_array($CASHIERREGQ)) {
                  // store the info into variable so we will able to perform calculation later //
                  $cashierm = $row["CASHIERM"];
                  // putting commas into the digits //
                  echo number_format($row["CASHIERM"]);
                }
              }
              echo '</i> cashiers this month</span>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-briefcase" aria-hidden="true"></i> Total Admins </span>
              <div class="count">';
              // show the number of total Admin //
              if (mysqli_num_rows($ADMINTOTALQ) < 1) {
                  echo "-";
              } else {
                if ($row = mysqli_fetch_array($ADMINTOTALQ)) {
                  // putting commas into the digits //
                  echo number_format($row["ADMIN"]);
                }
              }
              echo '</div>
              <span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i>';
              // show the number of cashier registered this month //
              if (mysqli_num_rows($ADMINREGQ) < 1) {
                  echo "-";
              } else {
                if ($row = mysqli_fetch_array($ADMINREGQ)) {
                  // store the info into variable so we will able to perform calculation later //
                  $adminm = $row["ADMINM"];
                  // putting commas into the digits //
                  echo number_format($row["ADMINM"]);
                }
              }
              echo '</i> admins this month</span>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-shopping-cart"></i> Highest Sales </span>
              <div class="count">';
              // show the name of the brand with highest sale this month //
              // first we will have to check who's having the top sales //
              if (mysqli_num_rows($HIGHSALEQ) < 1) {
                  echo "-";
              } else {
                if ($row = mysqli_fetch_array($HIGHSALEQ)) {
                  // then we take in the brand's ID to echo the name at the latter step //
                  $meal_brand_id = $row["meal_brand_id"];
                  // and we put the number of sales into a variable to echo it out later //
                  $sales = $row["BRAND_SALES"];
                  // Check and obtain the name of the brand //
                  $TOPBRAND = "SELECT brand_name FROM meal_brand WHERE brand_id = '$meal_brand_id'";
                  $TOPBRANDQ = mysqli_query($con, $TOPBRAND);
                  if (mysqli_num_rows($TOPBRANDQ) < 1) {
                      echo "-";
                  } else {
                    if ($row = mysqli_fetch_array($TOPBRANDQ)) {
                      // Uppercase the brand's name and show it out //
                      echo strtoupper($row["brand_name"]);
                    }
                  }
                }
              }
              echo '</div>
              <span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i>';
              // echo out the number of sales that we put into the variable just now //
              // putting commas into the digits //
              echo number_format($sales);
              echo '</i> sales for now</span>
            </div>
          </div>
          <!-- /top tiles -->

          <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="dashboard_graph">

                <div class="row x_title">
                  <div class="col-md-6">
                    <h3>Registered User<small> (Chart) </small></h3>
                  </div>
                </div>

                <div class="col-md-9 col-sm-9 col-xs-12">
                  <!-- Extra links for the graph -->
                  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
                  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
                  <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
                  <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>';
                 // now showing the graph related to the user //
                 // declaring the SQL code to call out the information //
                 $SLGRAPH = "SELECT COUNT(*) AS TOTAL, MONTH(registered_date) AS MONTH, YEAR(registered_date) AS YEAR, status
                              FROM user
                              WHERE registered_date > DATE_SUB(now(), INTERVAL 6 MONTH)
                              GROUP BY MONTH(registered_date), status
                              ORDER BY YEAR(registered_date) ASC";
                 $SLGRAPHQ = mysqli_query($con, $SLGRAPH);
                 $chart_data = "";

                while($row = mysqli_fetch_array($SLGRAPHQ)) {
                  // putting variables for the specific data //
                  $status = $row["status"];
                  if ($status == 0) {
                    $studgraph = $row["TOTAL"];
                  } else {
                    $studgraph = 0;
                  }
                  if ($status == 1) {
                    $cashgraph = $row["TOTAL"];
                  } else {
                    $cashgraph = 0;
                  }
                  if ($status == 2) {
                    $admingraph = $row["TOTAL"];
                  } else {
                    $admingraph = 0;
                  }
                  $chart_data .= "{ year:'".$row["YEAR"]."-".$row["MONTH"]."', student:".$studgraph.", cashier:".$cashgraph.", admin:".$admingraph."}, ";
                }
                  $chart_data = substr($chart_data, 0, -2);

                echo '<div class="chart-container"><div id="chart" style="min-width:100%; min-height:100%;"></div></div>
                </div>
                <script>
                  var myLineChart = Morris.Line({
                   element : "chart",
                   data:['.$chart_data.'],
                   xkey:"year",
                   ykeys:["student", "cashier", "admin"],
                   labels:["Student", "Cashier", "Admin"],
                   hideHover:"auto",
                   lineColors: ["#9B59B6","#E74C3C","#337AB7"],
                   stacked:true,
                   resize:true
                  });
                  // reload the linechart //
                  function reloadLine() {
                    var myLineChart = Morris.Line({
                     element : "chart",
                     data:['.$chart_data.'],
                     xkey:"year",
                     ykeys:["student", "cashier", "admin"],
                     labels:["Student", "Cashier", "Admin"],
                     hideHover:"auto",
                     lineColors: ["#9B59B6","#E74C3C","#337AB7"],
                     stacked:true,
                     resize:true
                    });
                  }
                </script>
                <div class="col-md-3 col-sm-3 col-xs-12 bg-white">
                  <div class="x_title">
                    <h2>Registered User\'s Status ('.$month_name.')</h2>
                    <div class="clearfix"></div>
                  </div>

                  <div class="col-md-12 col-sm-12 col-xs-6">
                    <div>
                      <p>Admin</p>
                      <div class="">
                        <div class="progress progress_sm" style="width: 76%;">
                          <div class="progress-bar bg-black" role="progressbar" data-transitiongoal="';
                          // calculate the percentage of the account status //
                          // Formula: Specific Account Status /Total User of the Month //
                          // Admin Percentage //
                          $adminpercent = (($adminm / $totaluser) * 100);
                          echo $adminpercent;
                          echo '"></div>
                        </div>
                      </div>
                    </div>
                    <div>
                      <p>Cashier</p>
                      <div class="">
                        <div class="progress progress_sm" style="width: 76%;">
                          <div class="progress-bar bg-red" role="progressbar" data-transitiongoal="';
                          // calculate the percentage of the account status //
                          // Formula: Specific Account Status /Total User of the Month //
                          // Cashier Percentage //
                          $cashierpercent = (($cashierm / $totaluser) * 100);
                          echo $cashierpercent;
                          echo '"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12 col-sm-12 col-xs-6">
                    <div>
                      <p>Student</p>
                      <div class="">
                        <div class="progress progress_sm" style="width: 76%;">
                          <div class="progress-bar bg-purple" role="progressbar" data-transitiongoal="';
                          // calculate the percentage of the account status //
                          // Formula: Specific Account Status /Total User of the Month //
                          // Student Percentage //
                          $studentpercent = (($studentm / $totaluser) * 100);
                          echo $studentpercent;
                          echo '"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="clearfix"></div>
              </div>
            </div>
          </div>

          <br/>

          <div class="row">
            <div class="">
            <!-- class: col-md-4 col-sm-4 col-xs-12 removed due to incompatible -->
              <div class="x_panel tile">
                <div class="x_title">
                  <h2>Meal Sales</h2>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <h4>Top 10 Meal Sales of the Month</h4>';
                  // calling out the top 10 meals accordingly //
                  // first we calculate and put the total meal sold into a variable //
                  if (mysqli_num_rows($TOTALMEALQ) < 1) {
                      $totalmeal = 0;
                  } else {
                    if ($row = mysqli_fetch_array($TOTALMEALQ)) {
                      // store the info into variable so we will able to perform calculation later //
                      $totalmeal = $row["TOTAL_MEAL_SOLD"];
                    }
                  }
                  // then only starts to generate the result //
                  $result = $con->query($TOP10MEAL);
                  if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                      $mealsold = $row["MEAL_SOLD"];
                      echo '<div class="widget_summary">
                              <div class="w_left w_25">
                                <span style="min-width: 100%; width: 250px; word-wrap: break-word;">';
                      echo $row["meal_name"];
                      echo '</span>
                            </div>
                            <div class="w_center w_55">
                                <div class="progress">
                                  <div class="progress-bar bg-green" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: ';
                      // now showing the percentage of the meal occured in the top 10 meals //
                      // Formula: Meal Sold / Total Meal Sold //
                      $mealpercent = ($mealsold / $totalmeal) * 100;
                      echo $mealpercent;
                      echo '%">
                            <span class="sr-only">60% Complete</span>
                          </div>
                        </div>
                      </div>
                      <div class="w_right w_20">
                        <span>';
                     // show the related meal sold percentage //
                        echo number_format((float)$mealpercent, 2, '.', '');
                      echo '%</span>
                         </div>
                         <div class="clearfix"></div>
                       </div>';
                    }
                  } else {
                    echo '<span style="color:red;">No Result</span>';
                  }

                  echo '
                    <div class="clearfix"></div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-4 col-sm-4 col-xs-12">
              <div class="x_panel tile overflow_hidden" style="width:77vw!important; min-width:100%; z-index:1000">
                <div class="x_title">
                  <h2>Brand\'s Total Sales</h2>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <table class="top10brand" style="width:100%">
                    <tr>
                      <th style="width:37%;">
                        <p>Top 5</p>
                      </th>
                      <th>
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                          <p class="">Brand</p>
                        </div>
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                          <p class="">Sales</p>
                        </div>
                      </th>
                    </tr>
                    <tr>
                      <td>';
                      // showing the Drilldown Charts now related top 5 brand with the highest sale //
                      // Formula: Brand's Total Meal Sold / ALL Total Meal Sold (All Brand Together) //
                      // declaring the SQL code to call out the information //
                      // P.S: Total Meal being sold has been define above: $TOTALMEALQ //
                      $DONUTGRAPH = "SELECT meal.meal_brand_id, meal_brand.brand_name,
                                     SUM((meal.meal_additional_quantity + meal.meal_default_quantity) - meal.meal_quantity) AS BRAND_MEAL_SOLD
                                     FROM meal INNER JOIN meal_brand
                                     ON meal.meal_brand_id = meal_brand.brand_id
                                     GROUP BY meal.meal_brand_id
                                     ORDER BY BRAND_MEAL_SOLD DESC
                                     LIMIT 5 ";
                      $DONUTGRAPHQ = mysqli_query($con, $DONUTGRAPH);

                      $data = array();
                      // first we will have to check who's having the top sales //
                      if (mysqli_num_rows($DONUTGRAPHQ) < 1) {
                          echo "-";
                      } else {
                        $otherspercent ="100";
                      // then we take in the brand's name and the number of meal sold in array //
                        while ($row = mysqli_fetch_array($DONUTGRAPHQ)) {
                          if ($row["BRAND_MEAL_SOLD"] > 0) {
                            // if the value is more than 0 then we will perform the calculation //
                            $brandpercent = number_format((float)((($row["BRAND_MEAL_SOLD"]) / $totalmeal) * 100), 2, '.', '');
                            $otherspercent = $otherspercent - $brandpercent;
                            $data[] = array(
                            'label' => $row["brand_name"],
                            'value' => $brandpercent
                            );
                          } else {
                            // otherwise we ignore it and make it as 0 //
                            $brandpercent = 0;
                            $brandpercent = number_format((float)$brandpercent, 2, '.', '');
                            $otherspercent = $otherspercent - $brandpercent;
                            $data[] = array(
                            'label' => $row["brand_name"],
                            'value' => $brandpercent
                            );
                          }
                        }
                        if ($otherspercent == 100) {
                          // if the $otherspercent is 100 then it means theres NO SALES occured for everyone, therefore the variable should be logically 0 as well //
                          $otherspercent = 0;
                        } else if ($otherspercent < 0) {
                          $otherspercent = 0;
                        }
                        $otherspercent = number_format((float)$otherspercent, 2, '.', '');
                        $data[] = array(
                          'label' => 'Others',
                          'value' => $otherspercent
                        );
                      }
                      // put the $data array value into another array variable so we can call out the names of the brand later //
                      $brandarray = array();
                      $brandarray = $data;
                      $data = json_encode($data);
                      echo '
                      <!-- Donut Chart Script -->
                      <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
                      <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
                      <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
                      <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css" />
                      <script>
                      $(document).ready(function() {

                       var donut_chart = Morris.Donut({
                           element: "donutChart",
                           data: '.$data.',';
                      echo
                         'colors: ["#3498DB","#1ABB9C","#9B59B6","#9CC2CB","#E74C3C","#73879C"],
                          resize:true
                       });
                      });
                      </script>
                      <script>
                      // reload the Donut Graph //
                      function reloadDonut() {
                        var donut_chart = Morris.Donut({
                            element: "donutChart",
                            data: '.$data.',
                            colors: ["#3498DB","#1ABB9C","#9B59B6","#9CC2CB","#E74C3C","#73879C"],
                            resize:true
                        });
                      }
                      </script>
                      <div class="chart-container"><div id="donutChart" style="min-width:100%; min-height:100%; padding-left:2vw; padding-right:1vw;"></div></div>
                      </td>
                      <td>
                        <table class="tile_info top10list"">
                          <tr>
                            <td>
                              <p><i class="fa fa-square blue"></i>'.$brandarray[0]["label"].' </p>
                            </td>
                            <td>'.$brandarray[0]["value"].'%</td>
                          </tr>
                          <tr>
                            <td>
                              <p><i class="fa fa-square green"></i>'.$brandarray[1]["label"].' </p>
                            </td>
                            <td>'.$brandarray[1]["value"].'%</td>
                          </tr>
                          <tr>
                            <td>
                              <p><i class="fa fa-square purple"></i>'.$brandarray[2]["label"].' </p>
                            </td>
                            <td>'.$brandarray[2]["value"].'%</td>
                          </tr>
                          <tr>
                            <td>
                              <p><i class="fa fa-square aero"></i>'.$brandarray[3]["label"].' </p>
                            </td>
                            <td>'.$brandarray[3]["value"].'%</td>
                          </tr>
                          <tr>
                            <td>
                              <p><i class="fa fa-square red"></i>'.$brandarray[4]["label"].' </p>
                            </td>
                            <td>'.$brandarray[4]["value"].'%</td>
                          </tr>
                          <tr>
                            <td>
                              <p><i class="fa fa-square black"></i>Others </p>
                            </td>
                            <td>'.$otherspercent.'%</td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>';
?>
