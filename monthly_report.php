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
// get the info about the report //
if ((isset($_GET["brand_id"])) && (isset($_GET["month"])) && (isset($_GET["year"]))) {
  $brand_id = $_GET["brand_id"];
  $month_report = $_GET["month"];
  $year_report = $_GET["year"];
} else {
  $brand_id = NULL;
  $month_report = NULL;
  $year_report = NULL;
}

require('pdf/fpdf181/fpdf.php');

// A4 width: 219mm //
// default margin: 10mm each side //
// Writable Horizontal: 219 - (10 * 2) = 189mm //
$pdf = new FPDF('p', 'mm', 'A4');
$pdf -> AddPage();

// Setting up page title //
$BRANDNAME = "SELECT * FROM meal_brand WHERE active = 1 AND brand_id = $brand_id";
$BRANDNAMEQ = mysqli_query($con, $BRANDNAME);

if (mysqli_num_rows($BRANDNAMEQ) < 1) {
  // if we can't retrieve the data then //
  $brand_name = "-";
  $report_name = "-";
} else {
  // get into the variable //
  if ($row = mysqli_fetch_array($BRANDNAMEQ)) {
    // converting into month name //
    $month_name = date('F', mktime(0, 0, 0, $month_report, 10));
    $brand_name = $row["brand_name"];
    // then make the report name as a whole //
    $report_name = $brand_name." (".$month_name." ".$year_report.")";
  }
}

$pdf->SetTitle($report_name);

// Adding a custom font //
$pdf -> AddFont('Oswald', '', 'Oswald-Regular.php');
$pdf -> AddFont('Oswald', 'B', 'Oswald-Bold.php');

// Name and Topic of the Report //
// Setting up font's Style, Pattern, Size //
$pdf -> SetFont('Oswald', '', 36);

// Cell(Width, Height, Text, Border, End Line, [Align]) //
$pdf -> Cell(189, 22, 'MEAL DEBIT SYSTEM', 0, 1, 'C');

// Month of the Report //
// Setting up font's Style, Pattern, Size //
$pdf -> SetFont('Oswald', 'B', 16);

// Cell(Width, Height, Text, Border, End Line, [Align]) //
$pdf -> Cell(189, 12, $month_name, 0, 1, 'C');

// Label of the Report //
// Setting up font's Style, Pattern, Size //
$pdf -> SetFont('Oswald', '', 25);

// Cell(Width, Height, Text, Border, End Line, [Align]) //
$pdf -> Cell(189, 16, 'SALES REPORT', 0, 1, 'C');

// Report's Content //
// Setting up font's Style, Pattern, Size //
$pdf -> SetFont('Oswald', '', 14);

// Cell(Width, Height, Text, Border, End Line, [Align]) //
$pdf -> Cell(9, 8, 'NO. ', 1, 0, '');
$pdf -> Cell(25, 8, 'BRAND', 1, 0, 'C');
$pdf -> Cell(18, 8, 'MEAL ID', 1, 0, 'C');
$pdf -> Cell(83, 8, 'MEAL NAME', 1, 0, 'C');
$pdf -> Cell(24, 8, 'TOTAL SOLD', 1, 0, 'C');
$pdf -> Cell(30, 8, 'TOTAL PRICE', 1, 1, 'C'); // End of Line

// Setting up font's Style, Pattern, Size //
$pdf -> SetFont('Oswald', '', 14);

// Begin to retrieve the data of the related report //
$MONTHLYREPORT = "SELECT *
                  FROM monthly_report
                  WHERE meal_brand = $brand_id AND month_report = $month_report AND year_report = $year_report
                  ORDER BY YEAR(generated_time), MONTH(generated_time) DESC";
$MONTHLYREPORTQ = mysqli_query($con, $MONTHLYREPORT);
if (mysqli_num_rows($MONTHLYREPORTQ) < 1) {
  // if we can't retrieve the data then //
  $no = 1;
  $brand_name = "-";
  $meal_id = "-";
  $meal_name = "-";
  $ammount_sold = "-";
  $total_price = "-";
  $generated_time = "-";
  // Cell(Width, Height, Text, Border, End Line, [Align]) //
  $pdf -> Cell(9, 8, $no, 1, 0, '');
  $pdf -> Cell(25, 8, $brand_name, 1, 0, 'C');
  $pdf -> Cell(18, 8, $meal_id, 1, 0, 'C');
  $pdf -> Cell(83, 8, $meal_name, 1, 0, '');
  $pdf -> Cell(24, 8, $ammount_sold, 1, 0, 'C');
  $pdf -> Cell(30, 8, $total_price, 1, 1, 'C'); // End of Line
} else {
  // retrieving the data from the database //
  $result = $con->query($MONTHLYREPORT);
  if ($result->num_rows > 0) {
    // $n will start by default of 1 //
    $n = 1;
    while ($row = $result->fetch_assoc()) {
      $no = $n;

      $meal_id = $row["meal_id"];
      $meal_name = $row["meal_name"];
      $ammount_sold = $row["meal_quantity_total"];
      $total_price = $row["meal_cost_total"];

      // since the reports are generated at the same time, we just bring the datetime into variable without worrying it might confused the data //
      $generated_time = $date = date_create($row["generated_time"]);
      // convert the datetime format to Y/m/D only //
      $generated_time = date_format($generated_time,"d/m/y");

      // Set up a query to call the description of the meal //
      $MEALDES = "SELECT * FROM meal WHERE meal_id = $meal_id";
      $MEALDESQ = mysqli_query($con, $MEALDES);
      if (mysqli_num_rows($MEALDESQ) < 1) {
        // if we can't retrieve the data then //
        $meal_details = "-";
      } else {
        // get into the variable //
        if ($row = mysqli_fetch_array($MEALDESQ)) {
          $meal_details = $row["meal_details"];
        }
      }

      // Cell(Width, Height, Text, Border, End Line, [Align]) //
      $pdf -> Cell(9, 8, $no, 1, 0, '');
      $pdf -> Cell(25, 8, $brand_name, 1, 0, 'C');
      $pdf -> Cell(18, 8, $meal_id, 1, 0, 'C');
      $pdf -> Cell(83, 8, $meal_name, 1, 0, '');
      $pdf -> Cell(24, 8, $ammount_sold, 1, 0, 'C');
      $pdf -> Cell(30, 8, 'RM '.$total_price, 1, 1, 'C'); // End of Line

      // once the row is ended add the $n by 1 by marking the start of next No. //
      $n++;
    }
  }
}



// Setting up font's Style, Pattern, Size //
$pdf -> SetFont('Oswald', 'B', 14);

// Cell(Width, Height, Text, Border, End Line, [Align]) //
$pdf -> Cell(135, 8, '', 1, 0, '');
// now calculating the subtotal price of all the sold meals //
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

$pdf -> Cell(24, 8, 'Subtotal', 1, 0, 'C');
$pdf -> Cell(30, 8, 'RM '.$total_revenue, 1, 1, 'C'); // End of Line

// Date of the Report being generated //
// Setting up font's Style, Pattern, Size //
$pdf -> SetFont('Oswald', '', 14);

// Cell(Width, Height, Text, Border, End Line, [Align]) //
$pdf -> Cell(30, 8, 'Generated Date:', 0, 0, '');
$pdf -> Cell(159, 8, $generated_time, 0, 1, '');

// Showing the result //
$pdf -> Output();
?>
