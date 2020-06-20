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
$user_id = $_SESSION['user_id'];
echo '
<div class="nav-php-account">
	<nav class="drop-down">
  	<ul class="action-section-account">
		<li class="profile-photo"><a href="profile_page.html"><img class="profile-picture" src="';
		if (isset($_SESSION['profile_image'])) {
			echo $_SESSION['profile_image'];
		}
		echo '"></a></li>
		<li class="profile-name"><a class="profile-name-real" style="text-align:center!important;"><span id="profile-name">'
		. $_SESSION['first_name'] . $_SESSION['last_name'] .
		'&nbsp;&nbsp;<i class="fas fa-caret-down"></i></span></a>
			<ul id="dropdown-content" class="dropdown-content">
				<li class="dropdown-list"><a href="profile_page.html"> Profile Page </a></li>';
				if ($_SESSION['status'] == 0) {
				  echo '
				  <li class="dropdown-list"><a href="transaction_record.html"> Transaction History </a></li>
				  ';
				} else if ($_SESSION['status'] == 1) {
				  echo '
				  <li class="dropdown-list"><a href="cashier_page.html"> Cashier Panel </a></li>
				  ';
				} else if ($_SESSION['status'] == 2) {
				  echo '
				  <li class="dropdown-list"><a href="admin_panel.html"> Admin Panel </a></li>
				  ';
				}
				echo
				'<li class="dropdown-list logout"><a href="logout.php"> Log Out <i class="fas fa-sign-out-alt"></i></a></li>
			</ul>
		</li>
	</ul>
	</nav>
</div>';
?>
