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
?>

<form method="POST" action="">
<!-- Title of the table -->
<!-- Student List: Active Section -->
<div class="row x_title">
  <div class="col-md-6">
    <h3>Student <small>Active </small></h3>
  </div>
</div>

<?php
 // in order to use multi-variable we have to first define it, otherwise it will show SYNTAX error //
 $output_active = '';
 // now we start to define the query //
 $STUDENTA = "SELECT *
              FROM user INNER JOIN student
              ON user.user_id = student.user_id
              WHERE user.status = 0 AND user.active = 1
              LIMIT $page_count, 10";
 $STUDENTAQ = mysqli_query($con, $STUDENTA);
 $output_active .= '
      <div class="">
           <table class="table table-bordered">
                <tr>
                     <th width="10%">NO.</th>
                     <th width="10%">ID</th>
                     <th width="15%">FIRST NAME</th>
                     <th width="15%">LAST NAME</th>
                     <th width="10%">GENDER</th>
                     <th width="15%">DOB</th>
                     <th width="10%">BALANCE</th>
                     <th width="15%">LAST LOGIN</th>
                </tr>';
 $rows = mysqli_num_rows($STUDENTAQ);
 if($rows > 0) {
      while($row = mysqli_fetch_array($STUDENTAQ)) {
           // now defining the gender based on the value //
           if (($row["gender"]) == 0) {
             $gender = "MALE";
           } else if (($row["gender"]) == 1) {
             $gender = "FEMALE";
           } else if (($row["gender"]) == 2) {
             $gender = "OTHERS";
           } else {
             $gender = "-";
           }
           // converting datetime values into 12AM/PM //
           $dob = date('d/m/y h:i A', strtotime($row["dob"]));
           if ($row["last_login"] == NULL) {
             $last_login = " - ";
           } else {
             $last_login = date('d/m/y h:i A', strtotime($row["last_login"]));
           }
           // defining the value of the balance //
           if ($row["balance"] == NULL || $row["balance"] == "") {
             $balance = "0.00";
           } else {
             $balance = $row["balance"];
             // making the balance 2 decimal at least //
             $balance = number_format((float)$balance, 2, '.', '');
           }

           $output_active .= '
                <tr>
                     <td>
                       <label class="regid-container">
                         <input required type="checkbox" class="regid-chkbox" name="userid[]" onclick="checkRequired()" value="'.$row["user_id"].'">
                         <span class="checkmark"></span>
                       </label>
                     </td>
                     <td>'.$row["user_id"].'</td>
                     <td class="first_name_stud" data-id1="'.$row["user_id"].'" contenteditable>'.$row["first_name"].'</td>
                     <td class="last_name_stud" data-id2="'.$row["user_id"].'" contenteditable>'.$row["last_name"].'</td>
                     <td class="gender_stud" data-id3="'.$row["user_id"].'">'.$gender.'</td>
                     <td class="dob_stud" data-id4="'.$row["user_id"].'">'.$dob.'</td>
                     <td class="balance_stud" data-id6="'.$row["balance"].'"> RM '.$balance.' </td>
                     <td class="last_login_stud" data-id5="'.$row["user_id"].'">'.$last_login.'</td>
                </tr>
           ';
      }
 } else {
      $output_active .= '
				<tr>
					<td> - </td>
          <td> - </td>
					<td id="fname"> - </td>
					<td id="lname"> - </td>
          <td id="gender"> - </td>
          <td id="dob"> - </td>
          <td id="balance"> - </td>
          <td id="last_login"> - </td>
			  </tr>';
 }
 $output_active .= '</table>
      </div>';

 echo '<span id="result"></span>';
 echo $output_active;
?>

<br>
<br>
<!-- Student List: Inctive Section -->
<div class="row x_title">
  <div class="col-md-6">
    <h3>Student <small>Inactive </small></h3>
  </div>
</div>

<?php
 // in order to use multi-variable we have to first define it, otherwise it will show SYNTAX error //
 $output_inactive = '';
 // now we start to define the query //
 $STUDENTI = "SELECT *
              FROM user INNER JOIN student
              ON user.user_id = student.user_id
              WHERE user.status = 0 AND user.active = 0
              LIMIT $page_count, 10";
 $STUDENTIQ = mysqli_query($con, $STUDENTI);
 $output_inactive .= '
      <div class="">
           <table class="table table-bordered">
                <tr>
                     <th width="10%">NO.</th>
                     <th width="10%">ID</th>
                     <th width="15%">FIRST NAME</th>
                     <th width="15%">LAST NAME</th>
                     <th width="10%">GENDER</th>
                     <th width="15%">DOB</th>
                     <th width="10%">BALANCE</th>
                     <th width="15%">LAST LOGIN</th>
                </tr>';
 $rows = mysqli_num_rows($STUDENTIQ);
 if($rows > 0) {
      while($row = mysqli_fetch_array($STUDENTIQ)) {
           // now defining the gender based on the value //
           if (($row["gender"]) == 0) {
             $gender = "MALE";
           } else if (($row["gender"]) == 1) {
             $gender = "FEMALE";
           } else if (($row["gender"]) == 2) {
             $gender = "OTHERS";
           } else {
             $gender = "-";
           }
           // converting datetime values into 12AM/PM //
           $dob = date('d/m/y h:i A', strtotime($row["dob"]));
           if ($row["last_login"] == NULL) {
             $last_login = " - ";
           } else {
             $last_login = date('d/m/y h:i A', strtotime($row["last_login"]));
           }
           // defining the value of the balance //
           if ($row["balance"] == NULL || $row["balance"] == "") {
             $balance = "0.00";
           } else {
             $balance = $row["balance"];
             // making the balance 2 decimal at least //
             $balance = number_format((float)$balance, 2, '.', '');
           }

           $output_inactive .= '
                <tr>
                     <td>
                       <label class="regid-container">
                         <input required type="checkbox" class="regid-chkbox" name="userid[]" onclick="checkRequired()" value="'.$row["user_id"].'">
                         <span class="checkmark"></span>
                       </label>
                     </td>
                     <td>'.$row["user_id"].'</td>
                     <td class="first_name_stud" data-id1="'.$row["user_id"].'" contenteditable>'.$row["first_name"].'</td>
                     <td class="last_name_stud" data-id2="'.$row["user_id"].'" contenteditable>'.$row["last_name"].'</td>
                     <td class="gender_stud" data-id3="'.$row["user_id"].'">'.$gender.'</td>
                     <td class="dob_stud" data-id4="'.$row["user_id"].'">'.$dob.'</td>
                     <td class="balance_stud" data-id6="'.$row["balance"].'"> RM '.$balance.' </td>
                     <td class="last_login_stud" data-id5="'.$row["user_id"].'">'.$last_login.'</td>
                </tr>
           ';
      }
 } else {
      $output_inactive .= '
				<tr>
					<td> - </td>
          <td> - </td>
					<td id="fname"> - </td>
					<td id="lname"> - </td>
          <td id="gender"> - </td>
          <td id="dob"> - </td>
          <td id="balance"> - </td>
          <td id="last_login"> - </td>
			  </tr>';
 }
 $output_inactive .= '</table>
      </div>';

 echo $output_inactive;
?>

<br>
<br>
<center>
  <span style="color:red;">*Note: You can click on the rows to edit the information of the user and update instantaneously.</span>
</center>

<?php
// creating the page number based on the list's number, if its over 10 records then create 1 page //
// we first find how many records in the user list //
// since we have two types of data: Active and Inactive, we have to find both of them and compare them, then take the highest value out of all //
// first we find the value of Active ones //
$TOTUSEA = "SELECT COUNT(*) AS TOT_USE_A FROM user
            WHERE active = '1' AND status = '0'";
$TOTUSEAQ = mysqli_query($con, $TOTUSEA);
if (mysqli_num_rows($TOTUSEAQ) < 1) {
  // only 1 page needed //
  $total_usea = "1";
} else {
  // count how many pages are needed //
  if ($row = mysqli_fetch_array($TOTUSEAQ)) {
    $total_usea = $row["TOT_USE_A"];
    // then we divide the number of total user record by 10, then round up, i.e. 1.1 > 2; since any extra records will required 1 new page //
    $total_usea = ($total_usea / 10);
    $total_usea = ceil($total_usea);
  }
}

// after we got the value from the Active tables then we proceeds to find the value of the Inactives //
$TOTUSEI = "SELECT COUNT(*) AS TOT_USE_I FROM user
           WHERE active = '0' AND status = '0'";
$TOTUSEIQ = mysqli_query($con, $TOTUSEI);
if (mysqli_num_rows($TOTUSEIQ) < 1) {
  // only 1 page needed //
  $total_usei = "1";
} else {
  // count how many pages are needed //
  if ($row = mysqli_fetch_array($TOTUSEIQ)) {
    $total_usei = $row["TOT_USE_I"];
    // then we divide the number of total verification codes record by 10, then round up, i.e. 1.1 > 2; since any extra records will required 1 new page //
    $total_usei = ($total_usei / 10);
    $total_usei = ceil($total_usei);
  }
}
// now checking which category got the highest value //
if ($total_usea > $total_usei) {
 $page_numb = $total_usea;
} else if ($total_usea < $total_usei) {
 $page_numb = $total_usei;
} else {
 // if its neither then both variables got the same value //
 $page_numb = $total_usea;
}
 // now creating the page numbers //
 echo '<div class="center">
      <div class="pagination" id="page-div">
        <a class="page" onclick="studTable(this)" name="table-page" value="';
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
          echo '" onclick="studTable(this)" name="table-page" id="page-1" value="1"> 1 </a>';
 for ($n = 2; $n <= $page_numb; $n++) {
   echo '<a class="page page-number';
   // highlight the current page number if the $_GET["page"] is the current page //
   if ($page == $n) {
     echo ' active';
   }
   echo '" onclick="studTable(this)" name="table-page" id="page-'.$n.'" value="'.$n.'">'.$n.'</a>';
 }
 echo '<a class="page" name="table-page" onclick="studTable(this)" value="';
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
 <!-- Verification Form\'s Style -->
<link rel="stylesheet" href="vendor/login-sign-in/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
 <!-- Verification Form\'s Script -->
<script src="vendor/login-sign-in/js/index.js"></script>

<div class="verification-form">
 <div class="">
   <div class="">
     <div class="logmod__container">
       <ul class="logmod__tabs">
         <li data-tabtar="lgm-1" id="ver-action-dis" class="verification-action current"><a onclick="switchFormDis()">Enable/Disable</a></li>
         <li data-tabtar="lgm-2" id="ver-action-ena" class="verification-action"><a onclick="switchFormEna()">Top Up</a></li>
       </ul>
       <div class="logmod__tab-wrapper">
         <div class="logmod__tab lgm-1 show" id="ver-form-dis">
           <form method="POST" action="">
           <div class="logmod__heading">
             <span class="logmod__heading-subtitle">Enter your information to verify and <strong>enable/disable the record.</strong></span>
           </div>
           <div class="logmod__form">
             <div class="sminputs">
               <div class="input full" style="height:auto!important;">
                 <label class="string optional" for="user-account-code-dis">Action*</label>
                 <label class="radio-container"> ENABLE
                   <input required type="radio" checked="checked" value="1" id="radio-action-meal-e" name="stud-action">
                   <span class="checkmark-radio"></span>
                 </label>
                 <label class="radio-container"> DISABLE
                   <input required type="radio" value="0" id="radio-action-meal-d" name="stud-action">
                   <span class="checkmark-radio"></span>
                 </label>
               </div>
             </div>
               <div class="sminputs">
                 <div class="input full">
                   <label class="string optional" for="user-account-code-dis">Account Code*</label>
                   <input required class="string optional" name="dis-acccode" maxlength="8" minlength="8" id="user-account-code-dis" placeholder="Account Code" type="password" size="8" />
                 </div>
               </div>
               <div class="sminputs">
                 <div class="input string optional">
                   <label class="string optional" for="user-pw-dis">Password *</label>
                   <input required class="string optional first-pw" name="dis-pw" maxlength="255" id="user-pw-dis" onkeyup="check_verlist_dis();" placeholder="Password" type="password" size="50" />
                 </div>
                 <div class="input string optional">
                   <label class="string optional" for="user-pw-repeat-dis">Repeat password *</label>
                   <input required class="string optional" name="dis-pw-repeat" maxlength="255" id="user-pw-repeat-dis" onkeyup="check_verlist_dis();" placeholder="Repeat password" type="password" size="50" />
                 	<span class="hide-password" id="toggle-pw-dis" onclick="togglePassDisA();">SHOW</span>
                 </div>
               </div>
               <div class="simform__actions">
                 <input disabled class="sumbit" name="submit-list-action-student" style="font-family:oswald!important;" type="submit" id="submit-dis" value="Confirm Action" />
                 <div id="error-message-dis" style="font-size:17px;"></div>
                 <span class="simform__actions-sidetext">By enabling/disabling the records, you are here and agree our <a class="special" onclick="return false;" role="link">Terms & Conditions</a></span>
               </div>
           </div>
          </div>

       <div class="logmod__tab lgm-2" id="ver-form-ena">
       <form method="POST" action="">
         <div class="logmod__heading">
           <span class="logmod__heading-subtitle">Enter your information to verify and <strong>topup the user.</strong></span>
         </div>
         <div class="logmod__form">
             <div class="sminputs">
               <div class="input full">
                 <label class="string optional" for="user-account-code-ena">Account Code*</label>
                 <input class="string optional" name="ena-acccode" maxlength="8" minlength="8" id="user-account-code-reg" placeholder="Account Code" type="password" size="8" />
               </div>
             </div>
             <div class="sminputs">
               <div class="input string optional">
                 <label class="string optional" for="user-pw-reg">Password *</label>
                 <input class="string optional first-pw" name="ena-pw" maxlength="255" id="user-pw-reg" onkeyup="check_reglist_r();" placeholder="Password" type="password" size="50" />
               </div>
               <div class="input string optional">
                 <label class="string optional" for="user-pw-repeat-reg">Repeat password *</label>
                 <input class="string optional" name="ena-pw-repeat" maxlength="255" id="user-pw-repeat-reg" onkeyup="check_reglist_r();" placeholder="Repeat password" type="password" size="50" />
               	<span class="hide-password" id="toggle-pw-reg" onclick="togglePassReg();">SHOW</span>
               </div>
             </div>
             <div class="sminputs">
               <div class="input full">
                 <label class="string optional" for="student-top-up">Top Up Amount (RM)*</label>
                 <input disabled class="string optional" name="stud-top-up" onKeyDown="return false" min="5" step="5" value="0" id="student-top-up" type="number" />
               </div>
             </div>
             <div class="simform__actions">
               <input disabled class="sumbit" name="submit-list-topup-stud" style="font-family:oswald!important;" type="submit" id="submit-r" value="Enable" />
               <div id="error-message-r" style="font-size:17px;"></div>
               <span class="simform__actions-sidetext">By topup the user's balance, you are here and agree our <a class="special" onclick="return false;" role="link">Terms & Conditions</a></span>
             </div>
         </div>
        </div>

       </div>
     </div>
   </div>
  </div>
 </div>
</form>
