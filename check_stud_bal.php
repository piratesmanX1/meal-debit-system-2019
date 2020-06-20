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
// first we will check whether there's already the same meal registered within the order id //
$CHECKBAL = "SELECT *
              FROM user INNER JOIN student
              ON user.user_id = student.user_id
              WHERE account_id = '".$_POST["account_id"]."' AND user.active = 1";
$CHECKBALQ = mysqli_query($con, $CHECKBAL);
if (mysqli_num_rows($CHECKBALQ) < 1) {
  // if there's none of data retrieve then either the email is wrong or there's no such active user //
  echo "Notice: ID invalid, or there\'s no such active user within the database.";
} else {
  // if we're able to retrieve the related info then we will call the balance into $_SESSION //
  if ($row = mysqli_fetch_array($CHECKBALQ)) {
    if ((isset($row['balance'])) && ($row['balance']) != NULL) {
      $_SESSION["balance"] = $row['balance'];
      $convert = $_SESSION["balance"];
      $_SESSION["balance"] = number_format((float)$convert, 2, '.', '');
      echo "Notice: Student Balance imported.";
    } else {
      $_SESSION["balance"] = 0.00;
      echo "Notice: Student Balance NOT imported since it might be NULL or it\'s empty. It\'ll be treated as default 0 balance.";
    }
  }
}
?>
