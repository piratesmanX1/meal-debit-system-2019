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
if ((isset($_GET["order_id"]))) {
  $order_id = $_GET["order_id"];
}

require('pdf/fpdf181/fpdf.php');

// A4 width: 219mm //
// default margin: 10mm each side //
// Writable Horizontal: 219 - (10 * 2) = 189mm //
$pdf = new FPDF('p', 'mm', 'A4');
$pdf -> AddPage();

// Setting up page title //
if (isset($order_id)) {
  $BRANDNAME = "SELECT * FROM user_order INNER JOIN meal_brand
                ON user_order.brand_id = meal_brand.brand_id
                WHERE user_order.paid = 1 AND user_order.order_id = $order_id";
  $BRANDNAMEQ = mysqli_query($con, $BRANDNAME);

  if (mysqli_num_rows($BRANDNAMEQ) < 1) {
    // if we can't retrieve the data then //
    $brand_name = "-";
    $report_name = "-";
  } else {
    // get into the variable //
    if ($row = mysqli_fetch_array($BRANDNAMEQ)) {
      // converting into month name //
      $transaction_date = $row["transaction_date"];

      $dateValue = strtotime($transaction_date);
      $year = date("Y", $dateValue);
      $month = date("m", $dateValue);

      $month_name = date('F', mktime(0, 0, 0, $month, 10));
      $brand_name = $row["brand_name"];
      $brand_id = $row["brand_id"];
      // then make the report name as a whole //
      $report_name = $brand_name." Receipt (".$month_name." ".$year.")";
    }
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
$pdf -> Cell(189, 16, 'RECEIPT', 0, 1, 'C');

// Report's Content //
// Setting up font's Style, Pattern, Size //
$pdf -> SetFont('Oswald', '', 14);

// Cell(Width, Height, Text, Border, End Line, [Align]) //
$pdf -> Cell(9, 8, 'NO. ', 1, 0, '');
$pdf -> Cell(25, 8, 'BRAND', 1, 0, 'C');
$pdf -> Cell(18, 8, 'MEAL ID', 1, 0, 'C');
$pdf -> Cell(83, 8, 'MEAL NAME', 1, 0, 'C');
$pdf -> Cell(24, 8, 'QUANTITY', 1, 0, 'C');
$pdf -> Cell(30, 8, 'TOTAL PRICE', 1, 1, 'C'); // End of Line

// Setting up font's Style, Pattern, Size //
$pdf -> SetFont('Oswald', '', 14);

// Begin to retrieve the data of the related report //
$USERRECEIPT = "SELECT *
                  FROM transaction_record INNER JOIN meal
                  ON transaction_record.meal_id = meal.meal_id
                  WHERE transaction_record.order_id = $order_id AND transaction_record.meal_brand_id = $brand_id
                  ORDER BY transaction_id ASC";
$USERRECEIPTQ = mysqli_query($con, $USERRECEIPT);
if (mysqli_num_rows($USERRECEIPTQ) < 1) {
  // if we can't retrieve the data then //
  $no = 1;
  $brand_name = "-";
  $meal_id = "-";
  $meal_name = "-";
  $ammount_sold = "-";
  $total_price = "-";
  $transaction_date = "-";
  // Cell(Width, Height, Text, Border, End Line, [Align]) //
  $pdf -> Cell(9, 8, $no, 1, 0, '');
  $pdf -> Cell(25, 8, $brand_name, 1, 0, 'C');
  $pdf -> Cell(18, 8, $meal_id, 1, 0, 'C');
  $pdf -> Cell(83, 8, $meal_name, 1, 0, '');
  $pdf -> Cell(24, 8, $ammount_sold, 1, 0, 'C');
  $pdf -> Cell(30, 8, $total_price, 1, 1, ''); // End of Line

} else {
  // retrieving the data from the database //
  $result = $con->query($USERRECEIPT);
  if ($result->num_rows > 0) {
    // $n will start by default of 1 //
    $n = 1;
    // $total_cost will be defined as 0 at first //
    $total_cost = 0;
    while ($row = $result->fetch_assoc()) {
      $no = $n;

      $meal_id = $row["meal_id"];
      $meal_name = $row["meal_name"];
      $ammount_sold = $row["meal_quantity_cart"];
      // Formula: Quantity * Meal Price //
      $total_price = (($row["meal_quantity_cart"]) * ($row["meal_price"]));
      $total_price = number_format((float)$total_price, 2, '.', '');
      // since the reports are generated at the same time, we just bring the datetime into variable without worrying it might confused the data //
      $generated_time = $date = date_create($transaction_date);
      // convert the datetime format to Y/m/D only //
      $generated_time = date_format($generated_time,"d/m/y");

      // Cell(Width, Height, Text, Border, End Line, [Align]) //
      $pdf -> Cell(9, 8, $no, 1, 0, '');
      $pdf -> Cell(25, 8, $brand_name, 1, 0, 'C');
      $pdf -> Cell(18, 8, $meal_id, 1, 0, 'C');
      $pdf -> Cell(83, 8, $meal_name, 1, 0, '');
      $pdf -> Cell(24, 8, $ammount_sold, 1, 0, 'C');
      $pdf -> Cell(30, 8, 'RM '.$total_price, 1, 1, ''); // End of Line

      // once the row is ended add the $n by 1 by marking the start of next No. //
      $n++;

      // define the total cost of the entire meal //
      $total_cost = $total_cost + $total_price;
      $total_cost = number_format((float)$total_cost, 2, '.', '');
    }
  }
}



// Setting up font's Style, Pattern, Size //
$pdf -> SetFont('Oswald', 'B', 14);

// Cell(Width, Height, Text, Border, End Line, [Align]) //
$pdf -> Cell(135, 8, '', 1, 0, '');
// now Showing the subtotal price of all the sold meals //

$pdf -> Cell(24, 8, 'Subtotal', 1, 0, 'C');
$pdf -> Cell(30, 8, 'RM '.$total_cost, 1, 1, 'C'); // End of Line

// Date of the Report being generated //
// Setting up font's Style, Pattern, Size //
$pdf -> SetFont('Oswald', '', 14);

// Cell(Width, Height, Text, Border, End Line, [Align]) //
$pdf -> Cell(30, 8, 'Generated Date:', 0, 0, '');
$pdf -> Cell(159, 8, $generated_time, 0, 1, '');

// Showing the result //
$pdf -> Output();
?>
