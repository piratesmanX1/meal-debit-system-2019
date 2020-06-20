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
	if (empty($_SESSION['user_id'])) {
		echo "<script>alert('Notice: You still didn\'t log in yet, please login first to proceed to Profile Page.');";
		echo "window.location.href='logout.php';</script>";
	}
?>

<?php
// before we begin we have to check see the user's status, i.e. Student, Admin, or Cashier //
// first we define the database based on the type of user //
if ($_SESSION['status'] == 0) {
  $userdb = "student";
} else if ($_SESSION['status'] == 1) {
  $userdb = "cashier";
} else if ($_SESSION['status'] == 2) {
  $userdb = "admin";
} else {
  echo "<script>alert('Notice: Status of the user is undefined, please login and try again.');";
  echo "window.location.href='logout.php';</script>";
}

// now begin to call out the content of the user //
$PROFILEUSE = "SELECT *
               FROM user INNER JOIN `".$userdb."`
               ON user.user_id = `".$userdb."`.user_id
               WHERE user.active = 1 AND user.user_id = '".$_SESSION["user_id"]."'
               ";
$PROFILEUSEQ = mysqli_query($con, $PROFILEUSE);

if (mysqli_num_rows($PROFILEUSEQ) < 1) {
  echo "<script>alert('ALERT: Account Info couldn\'t found, will immediately terminate the process and return to homepage.');";
  echo "window.location.href='homepage.html';</script>";
} else {
  if ($row = mysqli_fetch_array($PROFILEUSEQ)) {
    // defining datetime format into 12AM/PM format //
    $register_date = $row['registered_date'];
    $register_date = date('d/m/y h:i A', strtotime($register_date));

    if (isset($row['dob'])) {
      $dob = $row['dob'];
      $dob = date('d/m/y h:i A', strtotime($dob));
    } else {
      $dob = "-";
    }
    echo '
    <div class="wrapper">
      <div class="additional-block estateinfo-block">
        <div class="box-container">
          <div class="image-container">
          <!-- Upload Image Section -->
            <div id="wrapper" class="img-container">
             <img id="output_image" onclick="viewInput()" src="'.$_SESSION["profile_image"].'" class="preview-img"/>
             <div class="upload-text-container">
              <center>
               <div class="upload-text"><i class="fa fa-camera" aria-hidden="true"></i><br></div>
              </center>
             </div>
             <input type="file" onchange="preview_image(event)" style="display: none;" name="meal-img-reg" data-id1="'.$_SESSION["user_id"].'" class="profile_pic" id="bran-image-reg">
            </div>
          </div>
          <div class="estate-info">
            <h2 style="font-size: 28px;"> '.$row["first_name"].' '.$row["last_name"].' </h2>
            <div class="item">
              <div class="value link email" contenteditable data-id2="'.$_SESSION["user_id"].'">'.$row["email"].'</div>
            </div>
            <div class="item">
              <div class="value link"><label class="last-login">Last Login: </label>'.$register_date.'</div>
            </div>
          </div>
          <ul class="buttons">';
          if ($_SESSION['status'] == 0) {
            echo
            '<li>
               <a href="transaction_record.html"><i class="icon-profile fas fa-edit"></i><span class="label-profile"> Transaction History </span></a>
             </li>
              <li>
                 <a href="forget_password_logged.php"><i class="fa fa-address-book" aria-hidden="true"></i><span class="label-profile"> Forget Password </span></a>
               </li>
             ';
          } else if ($_SESSION['status'] == 1) {
            echo
            '<li>
               <a href="cashier_page.html"><i class="icon-profile fas fa-edit"></i><span class="label-profile"> Cashier Page </span></a>
             </li>
             <li class="disable_user" data-id5="'.$_SESSION["user_id"].'">
                <a href="#"><i class="icon-profile fas fa-toggle-off"></i><span class="label-profile"> Disable Profile </span></a>
              </li>
             ';
          } else if ($_SESSION['status'] == 2) {
            echo
            '<li>
               <a href="admin_panel.html"><i class="icon-profile fas fa-edit"></i><span class="label-profile"> Admin Panel </span></a>
             </li>
             <li class="disable_user" data-id5="'.$_SESSION["user_id"].'">
                <a href="#"><i class="icon-profile fas fa-toggle-off"></i><span class="label-profile"> Disable Profile </span></a>
              </li>
             ';
          }
            echo
           '
            <li>
              <a href="homepage.html"><i class="icon-profile fas fa-home"></i><span class="label-profile"> Back to Homepage </span></a>
            </li>
          </ul>
        </div>
      </div>
      <div class="additional-block">
        <h2>
          Profile Info
        </h2>
        <div class="address-details">
          <div class="item">
            <div class="label">
              First Name
            </div>
            <div class="value first_name" contenteditable data-id3="'.$_SESSION["user_id"].'">'.$row["first_name"].'</div>
          </div>
          <div class="item">
            <div class="label">
              Last Name
            </div>
            <div class="value last_name" contenteditable data-id4="'.$_SESSION["user_id"].'">'.$row["last_name"].'</div>
          </div>
          <div class="item">
            <div class="label">
              Gender
            </div>
            <div class="value gender">';
            // defining the value of gender //
            if ($row["gender"] == 0) {
              echo " MALE ";
            } else if ($row["gender"] == 1) {
              echo " FEMALE ";
            } else if ($row["gender"] == 2) {
              echo " OTHERS ";
            } else {
              echo " - ";
            }
          echo
            '</div>
          </div>
          <div class="item">
            <div class="label">
              Day of Birth
            </div>
            <div class="value">'.$dob.'</div>
          </div>
          <div class="item">';
            if ($_SESSION['status'] == 0) {
              echo '
              <div class="label">
                Balance
              </div>
              <div class="value"> RM
              ';
              if (isset($row["balance"])) {
                echo $row["balance"];
              } else {
                echo " - ";
              }
            } else {
              echo '
              <div class="label">
                Account Status
              </div>
              <div class="value">
              ';
              if ($row["active"] == 0) {
                echo "INACTIVE";
              } else if ($row["active"] == 1) {
                echo "ACTIVE";
              } else {
                echo " - ";
              }
            }

            // making the words all capital letter //
            $acclev = strtoupper($userdb);
            // making the sensitive info to hide but not all //
            $hiddenaccode = preg_replace('/(?<!^)\S/', '*', $row['decrypted_account_code']);
            echo
            '</div>
          </div>
          <div class="item">
            <div class="label">
              Privilege
            </div>
            <div class="value">'.$acclev.'</div>
          </div>
         </div>
        </div>
        <div class="additional-block">
          <h2>Security Info</h2>
          <div class="address-details">
            <div class="item">
              <div class="label">
                Account Code
              </div>
              <div class="value">'.$hiddenaccode.'</div>
            </div>
            <div class="item">
              <div class="label">
                Access Code:
              </div>
              <div class="value">';
              if (isset($row["access_code"]) && (($row["access_code"]) != NULL)) {
                echo "ACTIVE";
              } else {
                echo "INACTIVE";
              }
              echo
              '</div>
            </div>
           </div>
           <center style="margin-bottom: 20px!important;color: red!important;font-size:14px!important">
            <span>*Note: You can edit your profile by clicking on the information.</span>
          </center>
        </div>
      </div>
    ';
  }
}
?>
