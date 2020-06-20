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
 <!-- Scrollable Table Style -->
 <link rel="stylesheet" type="text/css" href="vendor/Table_Fixed_Header/Table_Fixed_Header/vendor/animate/animate.css">
<!--===============================================================================================-->
 <link rel="stylesheet" type="text/css" href="vendor/Table_Fixed_Header/Table_Fixed_Header/vendor/select2/select2.min.css">
<!--===============================================================================================-->
 <link rel="stylesheet" type="text/css" href="vendor/Table_Fixed_Header/Table_Fixed_Header/vendor/perfect-scrollbar/perfect-scrollbar.css">
<!--===============================================================================================-->
 <link rel="stylesheet" type="text/css" href="vendor/Table_Fixed_Header/Table_Fixed_Header/css/util.css">
 <link rel="stylesheet" type="text/css" href="vendor/Table_Fixed_Header/Table_Fixed_Header/css/main.css">

 <form method="post" action="">
 <div class="row x_title">
   <div class="col-md-6">
     <h3>Registration List <small>';
     echo 'User</small></h3>
   </div>
 </div>
 		<div class="container-table100">
 			<div class="wrap-table100">
 				<div class="table100 ver1 m-b-110">
 					<div class="table100-head">
 						<table class="reg-table">
 							<thead>
 								<tr class="row100 head">
 									<th class="cell100 column1">No.</th>
 									<th class="cell100 column2">Name</th>
 									<th class="cell100 column3">Registration Date</th>
 									<th class="cell100 column4">Ver. Code</th>
 									<th class="cell100 column5">Acc. ID</th>
 								</tr>
 							</thead>
 						</table>
 					</div>

 					<div class="table100-body js-pscroll">
 						<table class="reg-table">
 							<tbody>';
                // Begin to bring in the data from the database //
                $REGUSER = "SELECT * FROM registration_list
                            WHERE verified = '0'
                            ORDER BY apply_date DESC
                            LIMIT $page_count,10";
                $result = $con->query($REGUSER);
                if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                    // Converting time to 12AM/PM format //
                    $register_date = $row['apply_date'];
                    $register_date = date('d/m/y h:i A', strtotime($register_date));
                    echo '
                    <tr class="row100 body">
                      <td class="cell100 column1 reg-table-td" style="min-width:100%; min-height:100%;">
                      <label class="regid-container">
                        <input required type="checkbox" class="regid-chkbox" name="regid[]" onclick="checkRequired()" value="'.$row["registration_id"].'">
                        <span class="checkmark"></span>
                      </label>
                      </td>
                      <td class="cell100 column2 reg-table-td" style="min-width:100%; min-height:100%;"> '.$row["first_name"].' '.$row["last_name"].' </td>
                      <td class="cell100 column3 reg-table-td" style="min-width:100%; min-height:100%;"> '.$register_date.' </td>
                      <td class="cell100 column4 reg-table-td" style="min-width:100%; min-height:100%;"> '.$row["verification_code"].' </td>
                      <td class="cell100 column5 reg-table-td" style="min-width:100%; min-height:100%;">TP '.$row["account_id"].'</td>
                    </tr>
                    ';
                  }
                } else {
                  echo '
                  <tr class="row100 body">
                    <td class="cell100 column1 reg-table-td"> - </td>
                    <td class="cell100 column2 reg-table-td"> - </td>
                    <td class="cell100 column3 reg-table-td"> - </td>
                    <td class="cell100 column4 reg-table-td"> - </td>
                    <td class="cell100 column5 reg-table-td"> - </td>
                  </tr>
                  ';
                }
               echo
                '
 							</tbody>
 						</table>
 					</div>
 				</div>
 			</div>
 		</div>
 ';

 // creating the page number based on the list's number, if its over 10 records then create 1 page //
 // we first find how many records in the registration list //
 $REGUSER = "SELECT COUNT(*) AS TOTAL_REG FROM registration_list
             WHERE verified = '0'";
 $REGUSERQ = mysqli_query($con, $REGUSER);
 if (mysqli_num_rows($REGUSERQ) < 1) {
   // only 1 page needed //
   $page_numb = "1";
 } else {
   // count how many pages are needed //
   if ($row = mysqli_fetch_array($REGUSERQ)) {
     $total_reg = $row["TOTAL_REG"];
     // then we divide the number of total registration record by 10, then round up, i.e. 1.1 > 2; since any extra records will required 1 new page //
     $page_numb = ($total_reg / 10);
     $page_numb = ceil($page_numb);
   }
 }
 // now creating the page numbers //
 echo '<div class="center">
      <div class="pagination" id="page-div">
        <a class="page" onclick="regTable(this)" name="table-page" value="';
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
          echo '" onclick="regTable(this)" name="table-page" id="page-1" value="1"> 1 </a>';
 for ($n = 2; $n <= $page_numb; $n++) {
   echo '<a class="page page-number';
   // highlight the current page number if the $_GET["page"] is the current page //
   if ($page == $n) {
     echo ' active';
   }
   echo '" onclick="regTable(this)" name="table-page" id="page-'.$n.'" value="'.$n.'">'.$n.'</a>';
 }
 echo '<a class="page" name="table-page" onclick="regTable(this)" value="';
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

      // showing the options of action //
          echo '
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
        <li data-tabtar="lgm-2" id="ver-action-reg" class="verification-action current"><a onclick="switchFormReg()">Approve</a></li>
        <li data-tabtar="lgm-1" id="ver-action-del" class="verification-action"><a onclick="switchFormDel()">Delete</a></li>
      </ul>
      <div class="logmod__tab-wrapper">
      <div class="logmod__tab lgm-1" id="ver-form-del">
        <form method="POST" action="">
        <div class="logmod__heading">
          <span class="logmod__heading-subtitle">Enter your information to verify and <strong>delete the record.</strong></span>
        </div>
        <div class="logmod__form">
            <div class="sminputs">
              <div class="input full">
                <label class="string optional" for="user-account-code-del">Account Code*</label>
                <input class="string optional" name="del-acccode" maxlength="8" minlength="8" id="user-account-code-del" placeholder="Account Code" type="password" size="8" />
              </div>
            </div>
            <div class="sminputs">
              <div class="input string optional">
                <label class="string optional" for="user-pw-del">Password *</label>
                <input class="string optional first-pw" name="del-pw" maxlength="255" id="user-pw-del" onkeyup="check_reglist_d();" placeholder="Password" type="password" size="50" />
              </div>
              <div class="input string optional">
                <label class="string optional" for="user-pw-repeat-del">Repeat password *</label>
                <input class="string optional" name="del-pw-repeat" maxlength="255" id="user-pw-repeat-del" onkeyup="check_reglist_d();" placeholder="Repeat password" type="password" size="50" />
              	<span class="hide-password" id="toggle-pw-del" onclick="togglePassDel();">SHOW</span>
              </div>
            </div>
            <div class="simform__actions">
              <input disabled class="sumbit" name="submit-list-del" style="font-family:oswald!important;" type="submit" id="submit-d" value="Confirm Removal" onclick="return delForm()" />
              <div id="error-message-d" style="font-size:17px;"></div>
              <span class="simform__actions-sidetext">By deleting the records, you are here and agree our <a class="special" onclick="return false;" role="link">Terms & Conditions</a></span>
            </div>
        </div>
       </div>

      <div class="logmod__tab lgm-2 show" id="ver-form-reg">
      <form method="POST" action="">
        <div class="logmod__heading">
          <span class="logmod__heading-subtitle">Enter your information to verify and <strong>approve the registration.</strong></span>
        </div>
        <div class="logmod__form">
            <div class="sminputs">
              <div class="input full">
                <label class="string optional" for="user-account-code-reg">Account Code*</label>
                <input required class="string optional" name="reg-acccode" maxlength="8" minlength="8" id="user-account-code-reg" placeholder="Account Code" type="password" size="8" />
              </div>
            </div>
            <div class="sminputs">
              <div class="input string optional">
                <label class="string optional" for="user-pw-reg">Password *</label>
                <input required class="string optional first-pw" name="reg-pw" maxlength="255" id="user-pw-reg" onkeyup="check_reglist_r();" placeholder="Password" type="password" size="50" />
              </div>
              <div class="input string optional">
                <label class="string optional" for="user-pw-repeat-reg">Repeat password *</label>
                <input required class="string optional" name="reg-pw-repeat" maxlength="255" id="user-pw-repeat-reg" onkeyup="check_reglist_r();" placeholder="Repeat password" type="password" size="50" />
              	<span class="hide-password" id="toggle-pw-reg" onclick="togglePassReg();">SHOW</span>
              </div>
            </div>
            <div class="simform__actions">
              <input disabled class="sumbit" name="submit-list-reg" style="font-family:oswald!important;" type="submit" id="submit-r" value="Approve Registration" />
              <div id="error-message-r" style="font-size:17px;"></div>
              <span class="simform__actions-sidetext">By approving the registrations, you are here and agree our <a class="special" onclick="return false;" role="link">Terms & Conditions</a></span>
            </div>
        </div>
       </div>
      </div>
    </div>
  </div>
</div>
</div>
</form>';
?>
