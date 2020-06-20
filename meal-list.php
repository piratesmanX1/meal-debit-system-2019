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
// the filteration of meal shown based on the option list //
if (isset($_GET["brand"])) {
  $brand = $_GET["brand"];
} else {
  $brand = "0";
}
// if there's no input data then we will treat the variable as 0, as there's no indication of filteration needed, then we will show everything: 0 //
if ($brand == "") {
  $brand_filter = 0;
} else {
  // else we take in the value of the $_GET into the $brand_filter. P.S: $brand_filter is $brand_id //
  $brand_filter = $brand;
}
?>

<?php
// if $brand_filter is 0 then we only take in the value of $_GET["page"] //
if ($brand_filter == 0) {
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
    // we will multiply the input data by 12, which will result changes in SQL query like "LIMIT 10, 12" or "LIMIT 20, 12" //
    $page_count = (($page - 1) * 12);
  }
}
?>

<!-- Flexbox Style -->
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>

<form method="post" action="" enctype="multipart/form-data">
<div class="row x_title">
    <div class="col-md-6">
      <h3>Meal List <small> Active </small></h3>
    </div>
</div>

<span id="result"></span>
<center>
<select class="role-select" id="brand-type" name="brand-types" style="width:30vw; height:4vh;" onchange="mealFilter()">
  <option value="0">ALL</option>
  <!-- call out the brand value -->
  <?php
    $BRANDTYPE = "SELECT * FROM meal_brand WHERE active = 1";
    $result = $con->query($BRANDTYPE);
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        echo '
        <option value="'.$row["brand_id"].'"';
        // if the $_GET["brand_filter"] is equal as the selected option, then call it as selected //
        if ($brand_filter == $row["brand_id"]) {
          echo 'Selected';
        }
        echo '>' .$row["brand_name"]. '</option>
        ';
      }
    }
  ?>
</select>
</center>
<br>
<div class="container">
    <div id="products" class="row list-group">
<?php
    // begin to call out the meals, 12 per page //
    // we have to define the filteration based on the value of $brand_filter //
    if ($brand_filter == 0) {
      // if its 0 then its calling all of the meals
      $BRANDA = "SELECT *
                 FROM meal INNER JOIN meal_brand
                 ON meal.meal_brand_id = meal_brand.brand_id
                 WHERE meal.active = 1
                 LIMIT $page_count, 12";
    } else {
      $BRANDA = "SELECT *
                 FROM meal INNER JOIN meal_brand
                 ON meal.meal_brand_id = meal_brand.brand_id
                 WHERE meal.active = 1 AND meal.meal_brand_id = $brand_filter";
    }
    $result = $con->query($BRANDA);
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        // now convert the datetime value to 12AM/PM format //
        $register_date = $row['registered_date'];
        $register_date = date('d/m/y h:i A', strtotime($register_date));
        // now define the path of the image //
        if (isset($row["meal_image"])) {
          $meal_image = $row["meal_image"];
        } else {
          $meal_image = "/APU/SDP/image/e3.png";
        }
        echo '
        <div class="item  col-xs-4 col-lg-4">
            <div class="thumbnail">
              <label class="regid-container">
                <input required type="checkbox" class="regid-chkbox" name="mealid[]" onclick="checkRequired();" value="'.$row["meal_id"].'">
                <span class="checkmark" style="position:absolute;top:12px!important;left:0!important;"></span>
              </label>
                <img class="group list-group-image" style="height:250px;width:400px;" src="'.$meal_image.'" alt="" />
                <div class="caption">
                    <h4 class="group inner list-group-item-heading">
                      <strong><center>'.$row["brand_name"].'</center></strong>
                    </h4>
                    <center><h2 class="meal_name" contenteditable data-id1="'.$row["meal_id"].'">'.$row["meal_name"].'</h2></center>
                    <p class="group inner list-group-item-text">
                      <center> RM <div style="display:inline;" id="price-meal" contenteditable data-id2="'.$row["meal_id"].'" class="meal_price" onkeypress="validate(event)">'.$row["meal_price"].' </div></center>
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

<div class="row x_title">
    <div class="col-md-6">
      <h3>Meal List <small> Inactive </small></h3>
    </div>
</div>

<br>
<div class="container">
    <div id="products" class="row list-group">
<?php
    // begin to call out the meals, 12 per page //
    // we have to define the filteration based on the value of $brand_filter //
    if ($brand_filter == 0) {
      // if its 0 then its calling all of the meals
      $BRANDA = "SELECT *
                 FROM meal INNER JOIN meal_brand
                 ON meal.meal_brand_id = meal_brand.brand_id
                 WHERE meal.active = 0
                 LIMIT $page_count, 12";
    } else {
      $BRANDA = "SELECT *
                 FROM meal INNER JOIN meal_brand
                 ON meal.meal_brand_id = meal_brand.brand_id
                 WHERE meal.active = 0 AND meal.meal_brand_id = $brand_filter";
    }
    $result = $con->query($BRANDA);
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        // now convert the datetime value to 12AM/PM format //
        $register_date = $row['registered_date'];
        $register_date = date('d/m/y h:i A', strtotime($register_date));
        // now define the path of the image //
        if (isset($row["meal_image"])) {
          $meal_image = $row["meal_image"];
        } else {
          $meal_image = "/APU/SDP/image/e3.png";
        }
        echo '
        <div class="item  col-xs-4 col-lg-4">
            <div class="thumbnail">
              <label class="regid-container">
                <input required type="checkbox" class="regid-chkbox" name="mealid[]" onclick="checkRequired();" value="'.$row["meal_id"].'">
                <span class="checkmark" style="position:absolute;top:12px!important;left:0!important;"></span>
              </label>
                <img class="group list-group-image" style="height:250px;width:400px;" src="'.$meal_image.'" alt="" />
                <div class="caption">
                    <h4 class="group inner list-group-item-heading">
                      <strong><center>'.$row["brand_name"].'</center></strong>
                    </h4>
                    <center><h2 contenteditable class="meal_name" data-id1="'.$row["meal_id"].'">'.$row["meal_name"].'</h2></center>
                    <p class="group inner list-group-item-text">
                      <center> RM <div style="display:inline;" id="price-meal" class="meal_price" onkeypress="validate(event) contenteditable data-id2="'.$row["meal_id"].'">'.$row["meal_price"].' </div></center>
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
  <span style="color:red;">*Note: You can click on the name or price to edit the meal's info.</span>
</center>
<br>
<br>
<?php
// if $brand_filter is 0 then we only take in the value of $_GET["page"] //
if ($brand_filter == 0) {
  // creating the page number based on the list's number, if its over 12 records then create 1 page //
  // we first find how many records in the meal list //
  // since we have two types of data: Active and Inactive, we have to find both of them and compare them, then take the highest value out of all //
  // first we find the value of Unavailable ones //
  $TOTMEALI = "SELECT COUNT(*) AS TOTAL_MEALI FROM meal
               WHERE active = '0'";
  $TOTMEALIQ = mysqli_query($con, $TOTMEALI);
  if (mysqli_num_rows($TOTMEALIQ) < 1) {
   // only 1 page needed //
   $total_meali = "1";
  } else {
   // count how many pages are needed //
   if ($row = mysqli_fetch_array($TOTMEALIQ)) {
     $total_meali = $row["TOTAL_MEALI"];
     // then we divide the number of total inactive record by 12, then round up, i.e. 1.1 > 2; since any extra records will required 1 new page //
     $total_meali = ($total_meali / 12);
     $total_meali = ceil($total_meali);
   }
  }
  // after we got the value from the Inactive tables then we proceeds to find the value of the availables //
  $TOTMEALA = "SELECT COUNT(*) AS TOTAL_MEALA FROM meal
               WHERE active = '1'";
  $TOTMEALAQ = mysqli_query($con, $TOTMEALA);
  if (mysqli_num_rows($TOTMEALAQ) < 1) {
   // only 1 page needed //
   $total_meala = "1";
  } else {
   // count how many pages are needed //
   if ($row = mysqli_fetch_array($TOTMEALAQ)) {
     $total_meala = $row["TOTAL_MEALA"];
     // then we divide the number of total active record by 12, then round up, i.e. 1.1 > 2; since any extra records will required 1 new page //
     $total_meala = ($total_meala / 12);
     $total_meala = ceil($total_meala);
   }
  }
  // now checking which category got the highest value //
  if ($total_meali > $total_meala) {
    $page_numb = $total_meali;
  } else if ($total_meali < $total_meala) {
    $page_numb = $total_meala;
  } else {
  // if its neither then both variables got the same value //
    $page_numb = $total_meali;
  }

  // now creating the page numbers //
  echo '<div class="center">
      <div class="pagination" id="page-div">
        <a class="page" onclick="mealTable(this)" name="table-page" value="';
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
          echo '" onclick="mealTable(this)" name="table-page" id="page-1" value="1"> 1 </a>';
  for ($n = 2; $n <= $page_numb; $n++) {
   echo '<a class="page page-number';
   // highlight the current page number if the $_GET["page"] is the current page //
   if ($page == $n) {
     echo ' active';
   }
   echo '" onclick="mealTable(this)" name="table-page" id="page-'.$n.'" value="'.$n.'">'.$n.'</a>';
  }
  echo '<a class="page" name="table-page" onclick="mealTable(this)" value="';
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
}
?>

<?php
    // showing the options of action //
          echo '
          <!-- Verification Form\'s Style -->
          <link rel="stylesheet" href="vendor/login-sign-in/css/style.css">
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
          <!-- Verification Form\'s Script -->
          <script src="vendor/login-sign-in/js/index.js"></script>

<div class="verification-form">
 <div class="" id="verform">
  <div class="">
    <div class="logmod__container">
      <ul class="logmod__tabs">
        <li data-tabtar="lgm-1" id="ver-action-dis" class="verification-action current"><a onclick="switchFormDisA()">Enable/Disable</a></li>
        <li data-tabtar="lgm-2" id="ver-action-res" class="verification-action"><a onclick="switchFormResA()">Edit</a></li>
        <li data-tabtar="lgm-3" id="ver-action-del" class="verification-action"><a onclick="switchFormDelA()">Delete</a></li>
        <li data-tabtar="lgm-4" id="ver-action-reg" class="verification-action"><a onclick="switchFormRegBran()">Register</a></li>
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
                <input required type="radio" checked="checked" value="1" id="radio-action-meal-e" name="meal-action">
                <span class="checkmark-radio"></span>
              </label>
              <label class="radio-container"> DISABLE
                <input required type="radio" value="0" id="radio-action-meal-d" name="meal-action">
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
              <input disabled class="sumbit" name="submit-list-action-meal" style="font-family:oswald!important;" type="submit" id="submit-dis" value="Confirm Action" />
              <div id="error-message-dis" style="font-size:17px;"></div>
              <span class="simform__actions-sidetext">By enabling/disabling the records, you are here and agree our <a class="special" onclick="return false;" role="link">Terms & Conditions</a></span>
            </div>
        </div>
       </div>
       <!-- Did not change any of the class from RESET to suit ENABLE (except input names) due to there is no need for such changes to improve the functionality -->
      <div class="logmod__tab lgm-2" id="ver-form-res">
      <form method="POST" action="">
        <div class="logmod__heading">
          <span class="logmod__heading-subtitle">Enter your information to verify and <strong>edit the meal.</strong></span>
        </div>
        <div class="logmod__form">
        <!-- Upload Image Section -->
         <center class="no-display" id="edit-info-meal-4">
           <div id="wrapper" class="img-container-meal">
            <img id="output_image_meal" src="image/e2.png" class="preview-img-meal"/>
            <div class="upload-text-container-meal">
              <div class="upload-text-meal"><i class="fa fa-camera" aria-hidden="true"></i><br> Click here to Upload </div>
            </div>
            <input type="file" onchange="preview_image_meal(event)" style="display: none;" name="meal-img" id="meal-image-edit">
           </div>
         </center>
            <div class="sminputs">
              <div class="input full" style="height:auto!important;">
                <label class="string optional" for="user-account-code-dis">Edit Categories*</label>
                <label class="radio-container"> EDIT MEAL\'S BRAND
                  <input required type="radio" checked="checked" value="0" id="radio-edit-meal-brand" name="meal-edit" onclick="editMeal();">
                  <span class="checkmark-radio"></span>
                </label>
                <label class="radio-container"> EDIT MEAL\'S INFO
                  <input required type="radio" value="1" id="radio-edit-meal-info" name="meal-edit" onclick="editMeal();">
                  <span class="checkmark-radio"></span>
                </label>
              </div>
            </div>
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
            <div class="sminputs" id="edit-brand-meal">
              <div class="input full" style="height:80px!important">
                <label class="string optional" for="vercode-role">BRAND *</label>
                <select class="role-select" id="brand-type-meal" name="meal-edit-brand" style="width:30vw; height:4vh;">
                  <option value="" selected disabled>BRAND</option>
                  <!-- call out the brand value -->
                  ';
                    $BRANDTYPE = "SELECT * FROM meal_brand WHERE active = 1";
                    $result = $con->query($BRANDTYPE);
                    if ($result->num_rows > 0) {
                      while ($row = $result->fetch_assoc()) {
                        echo '
                        <option value="'.$row["brand_id"].'">' .$row["brand_name"]. '</option>
                        ';
                      }
                    }
                echo
                '</select>
              </div>
            </div>
            <div class="sminputs no-display" id="edit-info-meal-1">
              <div class="input input string optional">
                <label class="string optional" for="meal-name-edit">Meal Name *</label>
                <input class="string optional meal-name" name="meal-name-edit" maxlength="255" id="meal-name-edit" placeholder="Meal Name"/>
              </div>
              <div class="input input string optional">
                <label class="string optional" for="meal-quantity-edit">Meal Default Quantity *</label>
                <input class="string optional meal-quantity" type="number" min="1" step="1" onkeypress="validate(event)" name="meal-quantity-edit" id="meal-quantity-edit" placeholder="00"/>
              </div>
            </div>
            <div class="sminputs no-display" id="edit-info-meal-2">
              <div class="input input string optional" style="height:80px!important">
                <label class="string optional" for="meal-edit-info-brand">BRAND *</label>
                <select class="role-select" id="brand-type-meal-info" name="meal-edit-info-brand" style="width:30vw; height:4vh;">
                  <option value="" selected disabled>BRAND</option>
                  <!-- call out the brand value -->
                  ';
                    $BRANDTYPE = "SELECT * FROM meal_brand WHERE active = 1";
                    $result = $con->query($BRANDTYPE);
                    if ($result->num_rows > 0) {
                      while ($row = $result->fetch_assoc()) {
                        echo '
                        <option value="'.$row["brand_id"].'">' .$row["brand_name"]. '</option>
                        ';
                      }
                    }
                echo
                '</select>
              </div>
              <div class="input input string optional" style="height:80px!important">
                <label class="string optional" for="meal-price-edit">Meal Price *</label>
                <input class="string optional meal-price" type="number" step="0.25" min="1" onkeypress="validate(event)" name="meal-price-edit" id="meal-price-edit" placeholder="RM 0.00"/>
              </div>
            </div>
            <div class="sminputs no-display" id="edit-info-meal-3">
              <div class="input full" style="height:30vh!important;">
                <label class="string optional" for="meal-details-edit">Meal Details*</label>
                <textarea style="height:20vh!important;width:68vw!important" class="string optional meal-details" type="textarea" name="meal-details-edit" id="meal-details-edit" placeholder="Meal Details" /></textarea>
              </div>
            </div>
            <div class="simform__actions">
              <input disabled class="sumbit" name="submit-list-edit-meal" style="font-family:oswald!important;" type="submit" id="submit-res" value="Confirm Edit" />
              <div id="error-message-res" style="font-size:17px;"></div>
              <span class="simform__actions-sidetext">By editing the brand, you are here and agree our <a class="special" onclick="return false;" role="link">Terms & Conditions</a></span>
            </div>
        </div>
        <center>
           <span id="edit-info-meal-5" class="no-display" style="color:red;">*Note: You have to check the checkbox above to get the info of the meal.</span>
        </center>
       </div>

       <div class="logmod__tab lgm-2" id="ver-form-del">
       <form method="POST" action="">
         <div class="logmod__heading">
           <span class="logmod__heading-subtitle">Enter your information to verify and <strong>delete the meal.</strong></span>
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
               <input disabled class="sumbit" name="submit-list-del-meal" style="font-family:oswald!important;" type="submit" id="submit-del" value="Confirm Deletion" />
               <div id="error-message-del" style="font-size:17px;"></div>
               <span class="simform__actions-sidetext">By deleting the meal, you are here and agree our <a class="special" onclick="return false;" role="link">Terms & Conditions</a></span>
             </div>
         </div>
        </div>
        <!-- Changed the ID of brand\'s registration section due to different format -->
       <div class="logmod__tab lgm-2" id="ver-form-reg">
       <form method="POST" action="">
         <div class="logmod__heading">
           <span class="logmod__heading-subtitle">Enter your information to verify and <strong>register a new meal.</strong></span>
         </div>
         <div class="logmod__form">
            <!-- Upload Image Section -->
            <center>
              <div id="wrapper" class="img-container">
               <img id="output_image" src="image/e2.png" class="preview-img"/>
               <div class="upload-text-container">
                 <div class="upload-text"><i class="fa fa-camera" aria-hidden="true"></i><br> Click here to Upload </div>
               </div>
               <input type="file" onchange="preview_image(event)" style="display: none;" name="meal-img-reg" id="bran-image-reg">
              </div>
            </center>
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
             <div class="sminputs">
              <div class="input input string optional">
                <label class="string optional" for="meal-name-edit">Meal Name *</label>
                <input class="string optional meal-name" name="meal-name-reg" maxlength="255" id="meal-name-reg" placeholder="Meal Name"/>
              </div>
              <div class="input input string optional">
                <label class="string optional" for="meal-quantity-edit">Meal Default Quantity *</label>
                <input class="string optional meal-quantity" type="number" min="1" step="1" onkeypress="validate(event)" name="meal-quantity-reg" id="meal-quantity-reg" placeholder="00"/>
              </div>
            </div>
            <div class="sminputs">
             <div class="input input string optional" style="height:80px!important">
               <label class="string optional" for="meal-reg-info-brand">BRAND *</label>
               <select class="role-select" id="brand-type-meal-reg" name="meal-reg-brand" style="width:30vw; height:4vh;">
                 <option value="" selected disabled>BRAND</option>
                 <!-- call out the brand value -->
                 ';
                   $BRANDTYPE = "SELECT * FROM meal_brand WHERE active = 1";
                   $result = $con->query($BRANDTYPE);
                   if ($result->num_rows > 0) {
                     while ($row = $result->fetch_assoc()) {
                       echo '
                       <option value="'.$row["brand_id"].'">' .$row["brand_name"]. '</option>
                       ';
                     }
                   }
               echo
               '</select>
             </div>
             <div class="input input string optional" style="height:80px!important">
               <label class="string optional" for="meal-price-reg">Meal Price *</label>
               <input class="string optional meal-price" type="number" step="0.25" min="1" onkeypress="validate(event)" name="meal-price-reg" id="meal-price-reg" placeholder="RM 0.00"/>
             </div>
           </div>
           <div class="sminputs">
             <div class="input full" style="height:30vh!important;">
               <label class="string optional" for="meal-details-reg">Meal Details*</label>
               <textarea style="height:20vh!important;width:68vw!important" class="string optional meal-details" type="textarea" name="meal-details-reg" id="meal-details-reg" placeholder="Meal Details" /></textarea>
             </div>
           </div>
            <div class="simform__actions">
             <input disabled class="sumbit" name="submit-list-reg-meal" style="font-family:oswald!important;" type="submit" id="submit-reg" value="Confirm Registration" />
               <div id="error-message-reg" style="font-size:17px;"></div>
             <span class="simform__actions-sidetext">By registering the new meal, you are here and agree our <a class="special" onclick="return false;" role="link">Terms & Conditions</a></span>
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
