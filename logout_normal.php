<?php
// we will only start the session with session_start() IF the session isn't started yet //
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<?php
  session_destroy();
  echo "<script>alert('You\'ve logged out. See you next time.');";
  echo "window.location.href='homepage.html';</script>";
  exit();
 ?>
