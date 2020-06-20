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
  // showing the number of notification, and the latest 4 user that just register //
  $_SESSION['regnot'] = "-";
  $REGNOT = "SELECT COUNT(*) AS TOTALREG FROM registration_list WHERE verified = '0'";
  $REGNOTQ = mysqli_query($con, $REGNOT);
  if (mysqli_num_rows($REGNOTQ) < 1) {
    $_SESSION['regnot'] = '0';
  } else {
    if ($row = mysqli_fetch_array($REGNOTQ)) {
      $_SESSION['regnot'] = $row['TOTALREG'];
    }
  }
  echo '
  <!-- Making the number of notification always get the latest info -->
  <script>
  function regNot() {
    document.getElementById("noti-number").innerHTML = "'.$_SESSION['regnot'].'";
  }
  window.setInterval(function(){
  /// call your function here
    regNot()
}, 1000);

  window.onload = regNot;
  </script>
  <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
    <i class="far fa-bell"></i>
    <span class="badge bg-green" id="reg-notification">
      <!-- Showing the number of registering user -->
      '.$_SESSION['regnot'].'
    </span>
  </a>
  <ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">';
        // now calling out the information of the first 4 registering user //
        $REGNOTINFO = "SELECT * FROM registration_list
                       WHERE verified = '0'
                       GROUP BY apply_date DESC
                       LIMIT 4";
        $result = $con->query($REGNOTINFO);
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            if (empty($row['image_profile'])) {
              $noti_image = "/APU/SDP/image/02.png";
            } else {
              $noti_image = $row['image_profile'];
            }
            // Converting time to 12AM/PM format //
            $register_date = $row['apply_date'];
            $register_date = date('d/m/y h:i A', strtotime($register_date));
            echo '
         <li><a>
           <span class="image">
            <img src="'.$noti_image.'" alt="Profile Image" id="noti-image" />
           </span>
           <span>
             <span>'.$row["first_name"].' '.$row["last_name"].'</span>
             <span class="time">'.$register_date.'</span>
           </span>
           <span class="message">
             Verification Code: '.$row["verification_code"].' <br>
             <div style="padding-left:15%;">Account ID: '.$row["account_id"].'</div>
           </span>
         </a></li>';
         }
        } else {
          echo '<div style="color:red; text-align:center;">No Result</div>';
        }

        echo '
        <li><div class="text-center tablink" id="register-view">
					<a id="view-register">
					 <strong>View Registration List</strong>
				 	 <i class="fa fa-angle-right"></i>
					</a>
			 </div></li>
		  </ul>
      <script type="text/javascript">
    		document.getElementById("view-register").addEventListener("click", function(e) {
    			check_reg();
          refresh();
    		});
        document.getElementById("register-view").addEventListener("click", function(e) {
          check_reg();
          refresh();
        });
    		function check_reg() {
    			document.getElementById("r3").checked = true;
        }
    	</script>
      ';
?>
