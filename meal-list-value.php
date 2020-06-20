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
if (isset($_GET["mealid"])) {
  $mealid = $_GET["mealid"];
} else {
  $mealid = NULL;
  // if theres no GET value then we direct back to admin panel //
  echo "<script>alert('Notice: Meal_ID undefined, now directing back to Admin Panel.');";
  echo "window.location.href='admin_panel.html';</script>";
}
?>

<?php
// calling out the values of the related meal //
if (isset($mealid)) {
  $MEALCONTENT = "SELECT * FROM meal WHERE meal_id = $mealid";
  $MEALCONTENTQ = mysqli_query($con, $MEALCONTENT);
  if (@mysqli_num_rows($MEALCONTENTQ) < 1) {
    // suppress the error message into the variable with @mysqli_num_rows//

    // if theres no GET value then we told the user //
    echo '<center style="color:red;">Notice: Meal\'s checkbox is not checked, please at least check one. </center>';
  } else {
     if ($row = mysqli_fetch_array($MEALCONTENTQ)) {
       $meal_name = $row["meal_name"];
       $meal_details = $row["meal_details"];
       $meal_price = $row["meal_price"];
       $meal_default_quantity = $row["meal_default_quantity"];
       $meal_brand_id = $row["meal_brand_id"];

       if (isset($row["meal_image"])) {
         $meal_image = $row["meal_image"];
       } else {
         $meal_image = "/APU/SDP/image/e1.png";
       }
     }


  echo '<div class="">
   <div class="">
     <div class="logmod__container">
       <ul class="logmod__tabs">
         <li data-tabtar="lgm-1" id="ver-action-dis" class="verification-action"><a onclick="switchFormDisA()">Enable/Disable</a></li>
         <li data-tabtar="lgm-2" id="ver-action-res" class="verification-action current"><a onclick="switchFormResA()">Edit</a></li>
         <li data-tabtar="lgm-3" id="ver-action-del" class="verification-action"><a onclick="switchFormDelA()">Delete</a></li>
         <li data-tabtar="lgm-4" id="ver-action-reg" class="verification-action"><a onclick="switchFormRegBran()">Register</a></li>
       </ul>
       <div class="logmod__tab-wrapper">
       <div class="logmod__tab lgm-1" id="ver-form-dis">
         <form method="POST" action="">
         <div class="logmod__heading">
           <span class="logmod__heading-subtitle">Enter your information to verify and <strong>enable/disable the record.</strong></span>
         </div>
         <div class="logmod__form">
           <div class="sminputs">
             <div class="input full" style="height:auto!important;">
               <label class="string optional" for="user-account-code-dis">Action*</label>
               <label class="radio-container"> ENABLE
                 <input type="radio" checked="checked" value="1" id="radio-action-meal-e" name="meal-action">
                 <span class="checkmark-radio"></span>
               </label>
               <label class="radio-container"> DISABLE
                 <input type="radio" value="0" id="radio-action-meal-d" name="meal-action">
                 <span class="checkmark-radio"></span>
               </label>
             </div>
           </div>
             <div class="sminputs">
               <div class="input full">
                 <label class="string optional" for="user-account-code-dis">Account Code*</label>
                 <input class="string optional" name="dis-acccode" maxlength="8" minlength="8" id="user-account-code-dis" placeholder="Account Code" type="password" size="8" />
               </div>
             </div>
             <div class="sminputs">
               <div class="input string optional">
                 <label class="string optional" for="user-pw-dis">Password *</label>
                 <input class="string optional first-pw" name="dis-pw" maxlength="255" id="user-pw-dis" onkeyup="check_verlist_dis();" placeholder="Password" type="password" size="50" />
               </div>
               <div class="input string optional">
                 <label class="string optional" for="user-pw-repeat-dis">Repeat password *</label>
                 <input class="string optional" name="dis-pw-repeat" maxlength="255" id="user-pw-repeat-dis" onkeyup="check_verlist_dis();" placeholder="Repeat password" type="password" size="50" />
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
       <div class="logmod__tab lgm-2 show" id="ver-form-res">
       <form method="POST" action="">
         <div class="logmod__heading">
           <span class="logmod__heading-subtitle">Enter your information to verify and <strong>edit the meal.</strong></span>
         </div>
         <div class="logmod__form">
         <!-- Upload Image Section -->
          <center id="edit-info-meal-4">
            <div id="wrapper" class="img-container-meal">
             <img id="output_image_meal" src="'.$meal_image.'" class="preview-img-meal"/>
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
                   <input required checked type="radio" value="1" id="radio-edit-meal-info" name="meal-edit" onclick="editMeal();">
                   <span class="checkmark-radio"></span>
                 </label>
               </div>
             </div>
             <div class="sminputs">
               <div class="input full">
                 <label class="string optional" for="user-account-code-res">Account Code*</label>
                 <input required class="string optional" name="enable-acccode" maxlength="8" minlength="8" id="user-account-code-res" placeholder="Account Code" type="password" size="8" />
               </div>
             </div>
             <div class="sminputs">
               <div class="input string optional">
                 <label class="string optional" for="user-pw-res">Password *</label>
                 <input required class="string optional first-pw" name="enable-pw" maxlength="255" id="user-pw-res" onkeyup="check_verlist_res();" placeholder="Password" type="password" size="50" />
               </div>
               <div class="input string optional">
                 <label class="string optional" for="user-pw-repeat-res">Repeat password *</label>
                 <input required class="string optional" name="enable-pw-repeat" maxlength="255" id="user-pw-repeat-res" onkeyup="check_verlist_res();" placeholder="Repeat password" type="password" size="50" />
                 <span class="hide-password" id="toggle-pw-res" onclick="togglePassResA();">SHOW</span>
               </div>
             </div>
             <div class="sminputs no-display" id="edit-brand-meal">
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
             <div class="sminputs" id="edit-info-meal-1">
               <div class="input input string optional">
                 <label class="string optional" for="meal-name-edit">Meal Name *</label>
                 <input class="string optional meal-name" name="meal-name-edit" maxlength="255" id="meal-name-edit" placeholder="Meal Name" value="'.$meal_name.'"/>
               </div>
               <div class="input input string optional">
                 <label class="string optional" for="meal-quantity-edit">Meal Default Quantity *</label>
                 <input class="string optional meal-quantity" type="number" min="1" step="1" onkeypress="validate(event)" name="meal-quantity-edit" id="meal-quantity-edit" placeholder="00" value="'.$meal_default_quantity.'"/>
               </div>
             </div>
             <div class="sminputs" id="edit-info-meal-2">
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
                         <option value="'.$row["brand_id"].'"';
                         if ($row["brand_id"] == $meal_brand_id) {
                           echo "selected";
                         }
                         echo
                         '>' .$row["brand_name"]. '</option>
                         ';
                       }
                     }
                 echo
                 '</select>
               </div>
               <div class="input input string optional" style="height:80px!important">
                 <label class="string optional" for="meal-price-edit">Meal Price *</label>
                 <input class="string optional meal-price" type="number" step="0.25" min="1" onkeypress="validate(event)" name="meal-price-edit" id="meal-price-edit" placeholder="RM 0.00" value="'.$meal_price.'"/>
               </div>
             </div>
             <div class="sminputs" id="edit-info-meal-3">
               <div class="input full" style="height:30vh!important;">
                 <label class="string optional" for="meal-details-edit">Meal Details*</label>
                 <textarea style="height:20vh!important;width:68vw!important" class="string optional meal-details" type="textarea" name="meal-details-edit" id="meal-details-edit" placeholder="Meal Details"/>'.$meal_details.'</textarea>
               </div>
             </div>
             <div class="simform__actions">
               <input disabled class="sumbit" name="submit-list-edit-meal" style="font-family:oswald!important;" type="submit" id="submit-res" value="Confirm Edit" />
               <div id="error-message-res" style="font-size:17px;"></div>
               <span class="simform__actions-sidetext">By editing the brand, you are here and agree our <a class="special" onclick="return false;" role="link">Terms & Conditions</a></span>
             </div>
         </div>
         <center>
            <span id="edit-info-meal-5" style="color:red;">*Note: You can leave the input empty if you don\'t want to edit some part of the meal\'s info.</span>
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
  </div>';
  }
}
?>
