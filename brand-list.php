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
  // we will multiply the input data by 6, which will result changes in SQL query like "LIMIT 10, 6" or "LIMIT 20, 6" //
  $page_count = (($page - 1) * 6);
}
?>

<!-- Flexbox Style -->
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>

<form method="post" action="" enctype="multipart/form-data">
<div class="row x_title">
    <div class="col-md-6">
      <h3>Brand List <small> Active </small></h3>
    </div>
</div>
<span id="result"></span>
<div class="container">
    <div id="products" class="row list-group">
<?php
    // begin to call out the brands, 6 per page //
    $BRANDA = "SELECT * FROM meal_brand
               WHERE active = 1
               LIMIT $page_count,6";
    $result = $con->query($BRANDA);
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        // now convert the datetime value to 12AM/PM format //
        $register_date = $row['registered_date'];
        $register_date = date('d/m/y h:i A', strtotime($register_date));
        // now define the path of the image //
        if (isset($row["brand_image"])) {
          $brand_image = $row["brand_image"];
        } else {
          $brand_image = "/APU/SDP/image/e3.png";
        }
        echo '
        <div class="item  col-xs-4 col-lg-4">
            <div class="thumbnail">
              <label class="regid-container">
                <input required type="checkbox" class="regid-chkbox" name="branid[]" onclick="checkRequired()" value="'.$row["brand_id"].'">
                <span class="checkmark" style="position:absolute;top:12px!important;left:0!important;"></span>
              </label>
                <img class="group list-group-image" style="height:250px;width:400px;" src="'.$brand_image.'" alt="" />
                <div class="caption">
                    <h4 class="group inner list-group-item-heading">
                      <center class="brand_name" contenteditable data-id1="'.$row["brand_id"].'">'.$row["brand_name"].'</center>
                    </h4>
                    <p class="group inner list-group-item-text">
                      <center> '.$register_date.' </center>
                    </p>
                </div>
            </div>
        </div>
        ';
      }
    } else {
      echo '
      <div class="item  col-xs-4 col-lg-4">
          <div class="thumbnail">
              <img class="group list-group-image" style="height:250px;width:400px;" src="image/e1.png" alt="" />
              <div class="caption">
                  <h4 class="group inner list-group-item-heading">
                    <center style="color:red;"> NO RESULT </center>
                  </h4>
                  <p class="group inner list-group-item-text">
                    <center> 1999/06/04 </center>
                  </p>
              </div>
          </div>
      </div>
      ';
    }
  echo '
  </div>
  ';
?>

<div class="row x_title">
    <div class="col-md-6">
      <h3>Brand List <small> Inactive </small></h3>
    </div>
</div>
<div class="container">
    <div id="products" class="row list-group">
<?php
    // begin to call out the brands, 6 per page //
    $BRANDA = "SELECT * FROM meal_brand
               WHERE active = 0
               LIMIT $page_count,6";
    $result = $con->query($BRANDA);
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        // now convert the datetime value to 12AM/PM format //
        $register_date = $row['registered_date'];
        $register_date = date('d/m/y h:i A', strtotime($register_date));
        // now define the path of the image //
        if (isset($row["brand_image"])) {
          $brand_image = $row["brand_image"];
        } else {
          $brand_image = "/APU/SDP/image/e3.png";
        }
        echo '
        <div class="item  col-xs-4 col-lg-4">
            <div class="thumbnail">
              <label class="regid-container">
                <input required type="checkbox" class="regid-chkbox" name="branid[]" onclick="checkRequired()" value="'.$row["brand_id"].'">
                <span class="checkmark" style="position:absolute;top:12px!important;left:0!important;"></span>
              </label>
                <img class="group list-group-image" style="height:250px;width:400px;" src="'.$brand_image.'" alt="" />
                <div class="caption">
                    <h4 class="group inner list-group-item-heading">
                      <center class="brand_name" contenteditable data-id1="'.$row["brand_id"].'">'.$row["brand_name"].'</center>
                    </h4>
                    <p class="group inner list-group-item-text">
                      <center> '.$register_date.' </center>
                    </p>
                </div>
            </div>
        </div>
        ';
      }
    } else {
      echo '
      <div class="item  col-xs-4 col-lg-4">
          <div class="thumbnail">
              <img class="group list-group-image" style="height:250px;width:400px;" src="image/e1.png" alt="" />
              <div class="caption">
                  <h4 class="group inner list-group-item-heading">
                    <center style="color:red;"> NO RESULT </center>
                  </h4>
                  <p class="group inner list-group-item-text">
                    <center> 1999/06/04 </center>
                  </p>
              </div>
          </div>
      </div>
      ';
    }
  echo '
  </div>
</div>
  ';
?>
<center>
  <span style="color:red;">*Note: You can click on the name to edit the brand's name.</span>
</center>
<?php
// creating the page number based on the list's number, if its over 6 records then create 1 page //
// we first find how many records in the brand list //
// since we have two types of data: Active and Inactive, we have to find both of them and compare them, then take the highest value out of all //
// first we find the value of Unavailable ones //
$TOTBRANI = "SELECT COUNT(*) AS TOTAL_BRANI FROM meal_brand
             WHERE active = '0'";
$TOTBRANIQ = mysqli_query($con, $TOTBRANI);
if (mysqli_num_rows($TOTBRANIQ) < 1) {
 // only 1 page needed //
 $total_brani = "1";
} else {
 // count how many pages are needed //
 if ($row = mysqli_fetch_array($TOTBRANIQ)) {
   $total_brani = $row["TOTAL_BRANI"];
   // then we divide the number of total inactive record by 6, then round up, i.e. 1.1 > 2; since any extra records will required 1 new page //
   $total_brani = ($total_brani / 6);
   $total_brani = ceil($total_brani);
 }
}
// after we got the value from the Inactive tables then we proceeds to find the value of the availables //
$TOTBRANA = "SELECT COUNT(*) AS TOTAL_BRANA FROM meal_brand
             WHERE active = '1'";
$TOTBRANAQ = mysqli_query($con, $TOTBRANA);
if (mysqli_num_rows($TOTBRANAQ) < 1) {
 // only 1 page needed //
 $total_brana = "1";
} else {
 // count how many pages are needed //
 if ($row = mysqli_fetch_array($TOTBRANAQ)) {
   $total_brana = $row["TOTAL_BRANA"];
   // then we divide the number of total active record by 6, then round up, i.e. 1.1 > 2; since any extra records will required 1 new page //
   $total_brana = ($total_brana / 6);
   $total_brana = ceil($total_brana);
 }
}
// now checking which category got the highest value //
if ($total_brani > $total_brana) {
  $page_numb = $total_brani;
} else if ($total_brani < $total_brana) {
  $page_numb = $total_brana;
} else {
// if its neither then both variables got the same value //
  $page_numb = $total_brani;
}

// now creating the page numbers //
echo '<div class="center">
    <div class="pagination" id="page-div">
      <a class="page" onclick="branTableA(this)" name="table-page" value="';
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
        echo '" onclick="branTable(this)" name="table-page" id="page-1" value="1"> 1 </a>';
for ($n = 2; $n <= $page_numb; $n++) {
 echo '<a class="page page-number';
 // highlight the current page number if the $_GET["page"] is the current page //
 if ($page == $n) {
   echo ' active';
 }
 echo '" onclick="branTable(this)" name="table-page" id="page-'.$n.'" value="'.$n.'">'.$n.'</a>';
}
echo '<a class="page" name="table-page" onclick="branTable(this)" value="';
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
        <li data-tabtar="lgm-1" id="ver-action-dis" class="verification-action current"><a onclick="switchFormDisA()">Disable</a></li>
        <li data-tabtar="lgm-2" id="ver-action-res" class="verification-action"><a onclick="switchFormResA()">Enable</a></li>
        <li data-tabtar="lgm-3" id="ver-action-del" class="verification-action"><a onclick="switchFormDelA()">Delete</a></li>
        <li data-tabtar="lgm-4" id="ver-action-reg" class="verification-action"><a onclick="switchFormRegBran()">Register</a></li>
      </ul>
      <div class="logmod__tab-wrapper">
      <div class="logmod__tab lgm-1 show" id="ver-form-dis">
        <form method="POST" action="">
        <div class="logmod__heading">
          <span class="logmod__heading-subtitle">Enter your information to verify and <strong>disable the record.</strong></span>
        </div>
        <div class="logmod__form">
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
              <input disabled class="sumbit" name="submit-list-dis-bran" style="font-family:oswald!important;" type="submit" id="submit-dis" value="Confirm Disable" />
              <div id="error-message-dis" style="font-size:17px;"></div>
              <span class="simform__actions-sidetext">By disabling the records, you are here and agree our <a class="special" onclick="return false;" role="link">Terms & Conditions</a></span>
            </div>
        </div>
       </div>
       <!-- Did not change any of the class from RESET to suit ENABLE (except input names) due to there is no need for such changes to improve the functionality -->
      <div class="logmod__tab lgm-2" id="ver-form-res">
      <form method="POST" action="">
        <div class="logmod__heading">
          <span class="logmod__heading-subtitle">Enter your information to verify and <strong>enable the brand.</strong></span>
        </div>
        <div class="logmod__form">
            <div class="sminputs">
              <div class="input full">
                <label class="string optional" for="user-account-code-res">Account Code*</label>
                <input class="string optional" name="enable-acccode" maxlength="8" minlength="8" id="user-account-code-res" placeholder="Account Code" type="password" size="8" />
              </div>
            </div>
            <div class="sminputs">
              <div class="input string optional">
                <label class="string optional" for="user-pw-res">Password *</label>
                <input class="string optional first-pw" name="enable-pw" maxlength="255" id="user-pw-res" onkeyup="check_verlist_res();" placeholder="Password" type="password" size="50" />
              </div>
              <div class="input string optional">
                <label class="string optional" for="user-pw-repeat-res">Repeat password *</label>
                <input class="string optional" name="enable-pw-repeat" maxlength="255" id="user-pw-repeat-res" onkeyup="check_verlist_res();" placeholder="Repeat password" type="password" size="50" />
              	<span class="hide-password" id="toggle-pw-res" onclick="togglePassResA();">SHOW</span>
              </div>
            </div>
            <div class="simform__actions">
              <input disabled class="sumbit" name="submit-list-enable-bran" style="font-family:oswald!important;" type="submit" id="submit-res" value="Confirm Enable" />
              <div id="error-message-res" style="font-size:17px;"></div>
              <span class="simform__actions-sidetext">By enabling the brand, you are here and agree our <a class="special" onclick="return false;" role="link">Terms & Conditions</a></span>
            </div>
        </div>
       </div>

       <div class="logmod__tab lgm-2" id="ver-form-del">
       <form method="POST" action="">
         <div class="logmod__heading">
           <span class="logmod__heading-subtitle">Enter your information to verify and <strong>delete the brand.</strong></span>
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
                 <input class="string optional first-pw" name="del-pw" maxlength="255" id="user-pw-del" onkeyup="check_verlist_del();" placeholder="Password" type="password" size="50" />
               </div>
               <div class="input string optional">
                 <label class="string optional" for="user-pw-repeat-del">Repeat password *</label>
                 <input class="string optional" name="del-pw-repeat" maxlength="255" id="user-pw-repeat-del" onkeyup="check_verlist_del();" placeholder="Repeat password" type="password" size="50" />
                 <span class="hide-password" id="toggle-pw-del" onclick="togglePassDelA();">SHOW</span>
               </div>
             </div>
             <div class="simform__actions">
               <input disabled class="sumbit" name="submit-list-del-bran" style="font-family:oswald!important;" type="submit" id="submit-del" value="Confirm Deletion" />
               <div id="error-message-del" style="font-size:17px;"></div>
               <span class="simform__actions-sidetext">By deleting the brand, you are here and agree our <a class="special" onclick="return false;" role="link">Terms & Conditions</a></span>
             </div>
         </div>
        </div>
        <!-- Changed the ID of brand\'s registration section due to different format -->
       <div class="logmod__tab lgm-2" id="ver-form-reg">
       <form method="POST" action="">
         <div class="logmod__heading">
           <span class="logmod__heading-subtitle">Enter your information to verify and <strong>register a new brand.</strong></span>
         </div>
         <div class="logmod__form">
            <!-- Upload Image Section -->
             <center>
               <div id="wrapper" class="img-container">
                <img id="output_image" src="image/e2.png" class="preview-img"/>
                <div class="upload-text-container">
                  <div class="upload-text"><i class="fa fa-camera" aria-hidden="true"></i><br> Click here to Upload </div>
                </div>
                <input type="file" onchange="preview_image(event)" style="display: none;" name="bran-img" id="bran-image-reg">
               </div>
             </center>
             <div class="sminputs">
               <div class="input full">
                 <label class="string optional" for="reg-bran-name">Brand Name*</label>
                 <input class="string optional" name="reg-bran-name" maxlength="255" id="bran-name-reg" placeholder="Brand Name" type="text" />
               </div>
             </div>
             <div class="sminputs">
               <div class="input full">
                 <label class="string optional" for="user-account-code-reg">Account Code*</label>
                 <input class="string optional" name="reg-acccode" maxlength="8" minlength="8" id="user-account-code-reg" placeholder="Account Code" type="password" size="8" />
               </div>
             </div>
             <div class="sminputs">
               <div class="input string optional">
                 <label class="string optional" for="user-pw-reg">Password *</label>
                 <input class="string optional first-pw" name="reg-pw" maxlength="255" id="user-pw-reg" onkeyup="check_verlist_reg();" placeholder="Password" type="password" size="50" />
               </div>
               <div class="input string optional">
                 <label class="string optional" for="user-pw-repeat-reg">Repeat password *</label>
                 <input class="string optional" name="reg-pw-repeat" maxlength="255" id="user-pw-repeat-reg" onkeyup="check_verlist_reg();" placeholder="Repeat password" type="password" size="50" />
               	<span class="hide-password" id="toggle-pw-reg" onclick="togglePassRegA();">SHOW</span>
               </div>
             </div>
             <div class="simform__actions">
               <input disabled class="sumbit" name="submit-list-reg-bran" style="font-family:oswald!important;" type="submit" id="submit-reg" value="Confirm Registration" />
               <div id="error-message-reg" style="font-size:17px;"></div>
               <span class="simform__actions-sidetext">By registering the new brand, you are here and agree our <a class="special" onclick="return false;" role="link">Terms & Conditions</a></span>
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
