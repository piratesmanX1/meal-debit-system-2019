<?php
// we will only start the session with session_start() IF the session isn"t started yet //
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
  // we will multiply the input data by 10, which will result changes in SQL query like "LIMIT 10, 10" or "LIMIT 20, 10" //
  $page_count = (($page - 1) * 10);
}

 echo '
 <!-- Table Highlight Vertical Style -->
 <link rel="stylesheet" type="text/css" href="vendor/Table_Highlight_Vertical_Horizontal/Table_Highlight_Vertical_Horizontal/vendor/animate/animate.css">
<!--===============================================================================================-->
 <link rel="stylesheet" type="text/css" href="vendor/Table_Highlight_Vertical_Horizontal/Table_Highlight_Vertical_Horizontal/vendor/select2/select2.min.css">
<!--===============================================================================================-->
 <link rel="stylesheet" type="text/css" href="vendor/Table_Highlight_Vertical_Horizontal/Table_Highlight_Vertical_Horizontal/vendor/perfect-scrollbar/perfect-scrollbar.css">
<!--===============================================================================================-->
 <link rel="stylesheet" type="text/css" href="vendor/Table_Highlight_Vertical_Horizontal/Table_Highlight_Vertical_Horizontal/css/util.css">
 <link rel="stylesheet" type="text/css" href="vendor/Table_Highlight_Vertical_Horizontal/Table_Highlight_Vertical_Horizontal/css/main.css">
<!--===============================================================================================-->

 <form method="post" action="">
 <div class="row x_title">
   <div class="col-md-6">
     <h3>Sales Report<small>';
     echo ' Monthly </small></h3>
   </div>
 </div>
 <div class="limiter">
 <div class="container-table100">
   <div class="wrap-table100">
     <div class="table100 ver1 m-b-110">
       <table data-vertable="ver1">
         <thead>
         <tr class="row100 head">
           <th class="column100 column1" data-column="column1"></th>
               <th class="column100 column2" data-column="column2">Report Name</th>
               <th class="column100 column3" data-column="column3">Month</th>
               <th class="column100 column4" data-column="column4">Year</th>
               <th class="column100 column5" data-column="column5">Generated Time</th>
               <th class="column100 column6" data-column="column6">Total Sales</th>
               <th class="column100 column7" data-column="column7">Total Revenue</th>
             </tr>
           </thead>
           <tbody>';
             $MONTHLYREPORT = "SELECT * FROM
                              (SELECT *
                              FROM monthly_report
                              GROUP BY meal_brand, YEAR(generated_time), MONTH(generated_time)
                              ORDER BY YEAR(generated_time), MONTH(generated_time) DESC) src
                              LIMIT $page_count, 10";
              $result = $con->query($MONTHLYREPORT);
              if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                  $brand_id = $row["meal_brand"];
                  $report_name = $row["report_name"];
                  $year_report = $row["year_report"];
                  $month_report = $row["month_report"];
                  $generated_time = $row["generated_time"];

                  $BRANDNAME = "SELECT * FROM meal_brand WHERE active = 1 AND brand_id = $brand_id";
                  $BRANDNAMEQ = mysqli_query($con, $BRANDNAME);

                  if (mysqli_num_rows($BRANDNAMEQ) < 1) {
                    // if we can't retrieve the data then //
                    $brand_name = "-";
                    $report_name = "-";
                  } else {
                    // get into the variable //
                    if ($row = mysqli_fetch_array($BRANDNAMEQ)) {
                      $brand_name = $row["brand_name"];
                      // then make the report name as a whole //
                      $report_name = $brand_name."<br> (".$report_name." ".$year_report.")";
                    }
                  }
                  // converting into month name //
                  $month_name = date('F', mktime(0, 0, 0, $month_report, 10));
                  // converting into 12AM/PM format //
                  $generated_time = date('d/m/y h:i A', strtotime($generated_time));
                  echo '<tr class="row100">
                        <td class="column100 column1" data-column="column1"><a class="report-link" href="monthly_report.php?brand_id='.$brand_id.'&month='.$month_report.'&year='.$year_report.'" target="_blank">'.$brand_name.'</a></td>
                        <td class="column100 column2" data-column="column2"><a class="report-link" href="monthly_report.php?brand_id='.$brand_id.'&month='.$month_report.'&year='.$year_report.'" target="_blank">'.$report_name.'</a></td>
                        <td class="column100 column3" data-column="column3"><a class="report-link" href="monthly_report.php?brand_id='.$brand_id.'&month='.$month_report.'&year='.$year_report.'" target="_blank">'.$month_name.'</a></td>
                        <td class="column100 column4" data-column="column4"><a class="report-link" href="monthly_report.php?brand_id='.$brand_id.'&month='.$month_report.'&year='.$year_report.'" target="_blank">'.$year_report.'</a></td>
                        <td class="column100 column5" data-column="column5"><a class="report-link" href="monthly_report.php?brand_id='.$brand_id.'&month='.$month_report.'&year='.$year_report.'" target="_blank">'.$generated_time.'</a></td>';
                  // now finding the total sales of the related brand //
                  // Formula: SUM of all meal_sold value where the brand id are the same //
                  $TOTALSALES = "SELECT
                                 SUM(meal_quantity_total)
                                 AS TOTAL_MEAL_SOLD
                                 FROM monthly_report
                                 WHERE meal_brand = $brand_id AND month_report = $month_report AND year_report = $year_report";
                  $TOTALSALESQ = mysqli_query($con, $TOTALSALES);
                  if (mysqli_num_rows($TOTALSALESQ) < 1) {
                  // if we can't retrieve the data then //
                    $total_sales = "-";
                  } else {
                  // get into the variable //
                    if ($row = mysqli_fetch_array($TOTALSALESQ)) {
                      $total_sales = $row["TOTAL_MEAL_SOLD"];
                    }
                  }

                  // now finding the total revenue of the related brand //
                  // Formula: SUM of all meal_cost_total value where the brand id are the same //
                  $TOTALREV = "SELECT
                               SUM(meal_cost_total)
                               AS TOTAL_REVENUE
                               FROM monthly_report
                               WHERE meal_brand = $brand_id AND month_report = $month_report AND year_report = $year_report";
                  $TOTALREVQ = mysqli_query($con, $TOTALREV);
                  if (mysqli_num_rows($TOTALREVQ) < 1) {
                  // if we can't retrieve the data then //
                    $total_revenue = "-";
                  } else {
                  // get into the variable //
                    if ($row = mysqli_fetch_array($TOTALREVQ)) {
                      $total_revenue = $row["TOTAL_REVENUE"];
                    }
                  }
                  echo  '<td class="column100 column6" data-column="column6"><a class="report-link" href="monthly_report.php?brand_id='.$brand_id.'&month='.$month_report.'&year='.$year_report.'" target="_blank">'.$total_sales.'</a></td>
                         <td class="column100 column7" data-column="column7"><a class="report-link" href="monthly_report.php?brand_id='.$brand_id.'&month='.$month_report.'&year='.$year_report.'" target="_blank"> RM '.$total_revenue.'</a></td>
                       </tr>';
                }
              } else {
                // if there is no result then call out empty table rows //
                $brand_id = NULL;
                $brand_name = "-";
                $report_name = "-";
                $month_name = "-";
                $month_report = "-";
                $year_report = "-";
                $generated_time = "-";
                $total_sales = "-";
                $total_revenue = "-";
                echo '
                <tr class="row100">
                  <td class="column100 column1" data-column="column1"><a class="report-link" href="monthly_report.php?brand_id='.$brand_id.'&month='.$month_report.'&year='.$year_report.'" target="_blank">'.$brand_name.'</a></td>
                  <td class="column100 column2" data-column="column2"><a class="report-link" href="monthly_report.php?brand_id='.$brand_id.'&month='.$month_report.'&year='.$year_report.'" target="_blank">'.$report_name.'</a></td>
                  <td class="column100 column3" data-column="column3"><a class="report-link" href="monthly_report.php?brand_id='.$brand_id.'&month='.$month_report.'&year='.$year_report.'" target="_blank">'.$month_name.'</a></td>
                  <td class="column100 column4" data-column="column4"><a class="report-link" href="monthly_report.php?brand_id='.$brand_id.'&month='.$month_report.'&year='.$year_report.'" target="_blank">'.$year_report.'</a></td>
                  <td class="column100 column5" data-column="column5"><a class="report-link" href="monthly_report.php?brand_id='.$brand_id.'&month='.$month_report.'&year='.$year_report.'" target="_blank">'.$generated_time.'</a></td>
                  <td class="column100 column6" data-column="column6"><a class="report-link" href="monthly_report.php?brand_id='.$brand_id.'&month='.$month_report.'&year='.$year_report.'" target="_blank">'.$total_sales.'</a></td>
                  <td class="column100 column7" data-column="column7"><a class="report-link" href="monthly_report.php?brand_id='.$brand_id.'&month='.$month_report.'&year='.$year_report.'" target="_blank"> RM '.$total_revenue.'</a></td>
                </tr>
                ';
              }
            echo '
         </tbody>
       </table>
     </div>
   </div>
   <span style="color:red;">*Note: You can click on the rows to view the details of the report.</span>
 </div>
</div>
 ';

 // creating the page number based on the list's number, if its over 10 records then create 1 page //
 // we first find how many records in the list //
 $TOTALREP = "SELECT COUNT(*) AS TOTAL_REPORT_TIME FROM
              (SELECT *
              FROM monthly_report
              GROUP BY meal_brand, YEAR(generated_time), MONTH(generated_time)
              ORDER BY YEAR(generated_time), MONTH(generated_time) DESC) src";
// P.S: src stands for alias, essential SYNTAX for sub-query above, otherwise it won't work //
 $TOTALREPQ = mysqli_query($con, $TOTALREP);
 if (mysqli_num_rows($TOTALREPQ) < 1) {
   // only 1 page needed //
   $page_numb = "1";
 } else {
   // count how many pages are needed //
   if ($row = mysqli_fetch_array($TOTALREPQ)) {
     $total_rep = $row["TOTAL_REPORT_TIME"];
     // then we divide the number of total record by 10, then round up, i.e. 1.1 > 2; since any extra records will required 1 new page //
     $page_numb = ($total_rep / 10);
     $page_numb = ceil($page_numb);
   }
 }

 // now creating the page numbers //
 echo '<div class="center">
      <div class="pagination" id="page-div">
        <a class="page" onclick="repTable(this)" name="table-page" value="';
        // if the current $_GET['page'] number is not more than 2 then it has no value, but 1 //
        if ($page < 2) {
          $previous_page = 1;
          echo $previous_page;
        } else {
          $previous_page = ($page - 1);
          echo ($page - 1);
        }
        echo '" id="page-'.$previous_page.'">&laquo;</a>
          <a class="page page-number';
          // highlight the current page number if the $_GET["page"] is the current page //
          if ($page == 1) {
            echo ' active';
          }
          echo '" onclick="repTable(this)" name="table-page" id="page-1" value="1"> 1 </a>';
 for ($n = 2; $n <= $page_numb; $n++) {
   echo '<a class="page page-number';
   // highlight the current page number if the $_GET["page"] is the current page //
   if ($page == $n) {
     echo ' active';
   }
   echo '" onclick="repTable(this)" name="table-page" id="page-'.$n.'" value="'.$n.'">'.$n.'</a>';
 }
 echo '<a class="page" name="table-page" onclick="repTable(this)" value="';
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
 echo '" id="page-'.$next_page.'">&raquo;</a>
       </div>
      </div>';
?>
