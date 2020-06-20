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
  // we will multiply the input data by 5, which will result changes in SQL query like "LIMIT 10, 5" or "LIMIT 20, 5" //
  $page_count = (($page - 1) * 5);
}
?>

<link rel="stylesheet" href="vendor/data-table/css/style.css">
  		<div class="table-title" style="padding-top:15vh!important">
        <h3>Transaction Record Table</h3>
      </div>
      <table class="table-fill" style="max-width: 1200px!important;">
        <thead>
          <tr>
            <th class="text-left">#</th>
            <th class="text-left">Order ID</th>
            <th class="text-left">Transaction Date</th>
            <th class="text-left">Brand</th>
            <th class="text-left">Total Cost</th>
          </tr>
        </thead>
        <tbody class="table-hover">

<?php
  // begin to take in the transaction record of the user //
if (isset($_SESSION["user_id"])) {
  // if there is then we start to define the query to get the content of the transaction_record //
  $TRANSCONT = "SELECT *
                FROM user_order INNER JOIN transaction_record
                ON user_order.order_id = transaction_record.order_id
                WHERE user_order.user_id = '".$_SESSION["user_id"]."' AND user_order.paid = 1
                GROUP BY user_order.order_id
                ORDER BY user_order.transaction_date DESC
                LIMIT $page_count, 5";
  $TRANSCONTQ = mysqli_query($con, $TRANSCONT);

  if (mysqli_num_rows($TRANSCONTQ) < 1) {
    // if we can't retrieve the data then //
    echo "
    <tr>
      <td class='text-left'> - </td>
      <td class='text-left'> - </td>
      <td class='text-left'> - </td>
      <td class='text-left'> - </td>
      <td class='text-left'> - </td>
    </tr>
    ";
  } else {
    $result = $con->query($TRANSCONT);
    if ($result->num_rows > 0) {
      $n = 1;
      while ($row = $result->fetch_assoc()) {
        // converting into 12AM/PM format //
        $transaction_date = $row["transaction_date"];
        $transaction_date = date('d/m/y h:i A', strtotime($transaction_date));
        // now search the name of the brand //
        $brand_id = $row["brand_id"];

        $BRANNAME = "SELECT * FROM meal_brand WHERE brand_id = '".$brand_id."'";
        $BRANNAMEQ = mysqli_query($con, $BRANNAME);
        if (mysqli_num_rows($BRANNAMEQ) < 1) {
          $brand_name = " - ";
        } else {
          if ($rows = mysqli_fetch_array($BRANNAMEQ)) {
            $brand_name = $rows['brand_name'];
          }
        }
        echo '
        <tr>
          <td class="text-left"><a style="text-decoration:none!important; color:#666B85!important" href="transaction_receipt.php?order_id='.$row['order_id'].'" target="_blank"> '.$n.' </a></td>
          <td class="text-left"><a style="text-decoration:none!important; color:#666B85!important" href="transaction_receipt.php?order_id='.$row['order_id'].'" target="_blank"> '.$row['order_id'].' </a></td>
          <td class="text-left"><a style="text-decoration:none!important; color:#666B85!important" href="transaction_receipt.php?order_id='.$row['order_id'].'" target="_blank"> '.$transaction_date.' </a></td>
          <td class="text-left"><a style="text-decoration:none!important; color:#666B85!important" href="transaction_receipt.php?order_id='.$row['order_id'].'" target="_blank"> '.$brand_name.' </a></td>
          <td class="text-left"><a style="text-decoration:none!important; color:#666B85!important" href="transaction_receipt.php?order_id='.$row['order_id'].'" target="_blank"> RM '.$row['total_price'].' </a></td>
        </tr>
        ';
        $n++;
      }
    }
  }
} else {
  // if theres no user_id then we inform and force the user to log out //
  echo "<script>alert('WARNING: User ID undefined, system will log you out due to security reason.');";
  echo "window.location.href='logout.php';</script>";
}
?>

        </tbody>
      </table>

<?php
if (mysqli_num_rows($TRANSCONTQ) < 1) {
  // if we can't retrieve the data then //
  echo "
  <br>
  <br>
  <center><span style='color:red!important; font-size:14px!important'> Notice: No Transaction Records available. </span></center>
  <br>
  ";
} else {
  echo "
  <br>
  <br>
  <center><span style='color:red!important; font-size:14px!important'> *Notice: You can view the details of the transaction by clicking on the rows. </span></center>
  <br>
  ";
}
?>

<?php
// creating the page number based on the list's number, if its over 10 records then create 1 page //
// we first find how many records in the list //
$TOTALREC = "SELECT COUNT(*) AS TOTAL_TRANSACTION_REC FROM
             (SELECT *
              FROM user_order
              WHERE user_id = '52' AND paid = 1
              GROUP BY order_id
              ORDER BY transaction_date DESC) src";
// P.S: src stands for alias, essential SYNTAX for sub-query above, otherwise it won't work //
$TOTALRECQ = mysqli_query($con, $TOTALREC);
if (mysqli_num_rows($TOTALRECQ) < 1) {
  // only 1 page needed //
  $page_numb = "1";
} else {
  // count how many pages are needed //
  if ($row = mysqli_fetch_array($TOTALRECQ)) {
    $total_rep = $row["TOTAL_TRANSACTION_REC"];
    // then we divide the number of total record by 5, then round up, i.e. 1.1 > 2; since any extra records will required 1 new page //
    $page_numb = ($total_rep / 5);
    $page_numb = ceil($page_numb);
  }
}

// now creating the page numbers //
echo '<div class="center">
     <div class="pagination" id="page-div">
       <a class="page" onclick="transTable(this)" style="color:white" name="table-page" value="';
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
         echo '" onclick="transTable(this)" style="color:white" name="table-page" id="page-1" value="1"> 1 </a>';
for ($n = 2; $n <= $page_numb; $n++) {
  echo '<a class="page page-number';
  // highlight the current page number if the $_GET["page"] is the current page //
  if ($page == $n) {
    echo ' active';
  }
  echo '" onclick="transTable(this)" style="color:white" name="table-page" id="page-'.$n.'" value="'.$n.'">'.$n.'</a>';
}
echo '<a class="page" name="table-page" style="color:white" onclick="transTable(this)" value="';
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
