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

 <!-- Material Table Style -->
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
 <link rel="stylesheet" href="vendor/material-design-responsive-table/css/style.css">
 <div id="demo">
   <h1>Admin List</h1>
   <h2>Active</h2>

   <!-- Responsive table starts here -->
   <!-- For correct display on small screens you must add 'data-title' to each 'td' in your table -->
   <div class="table-responsive-vertical shadow-z-1">
   <!-- Table starts here -->
   <table id="table" class="table table-hover table-mc-light-blue">
       <thead>
         <tr>
           <th>USER ID</th>
           <th>NAME</th>
           <th>EMAIL</th>
           <th>VER.CODE</th>
           <th>ACTIVE</th>
           <th>LAST LOGIN</th>
         </tr>
       </thead>
       <tbody>
      <?php
         // Begin to bring in the data of the admins: Active //
         $ADMINA = "SELECT *
                    FROM user INNER JOIN admin
                    ON user.user_id = admin.user_id
                    WHERE user.status = 2 AND user.active = 1
                    ORDER BY user.last_login DESC
                    LIMIT $page_count, 10";
         $result = $con->query($ADMINA);
         if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            // Converting time to 12AM/PM format //
            $last_login = $row['last_login'];
            $last_login = date('d/m/y h:i A', strtotime($last_login));
            // putting together user's full name //
            $full_name =  $row['first_name']." ".$row['last_name'];
            // define the status of the account //
            if (($row['active']) == 0) {
              $status = " N ";
            } else if (($row['active']) == 1) {
              $status = " Y ";
            } else {
              $status = " - ";
            }
            echo '
            <tr>
              <td data-title="USER ID">'.$row["user_id"].'</td>
              <td data-title="NAME">'.$full_name.'</td>
              <td data-title="EMAIL">'.$row["email"].'</td>
              <td data-title="VERCODE">'.$row["verification_code"].'</td>
              <td data-title="STATUS">'.$status.'</td>
              <td data-title="LAST LOGIN">'.$last_login.'</td>
            </tr>
            ';
          }
         } else {
           echo '
           <tr>
             <td data-title="USER ID"> - </td>
             <td data-title="NAME"> - </td>
             <td data-title="EMAIL"> - </td>
             <td data-title="VERCODE"> - </td>
             <td data-title="STATUS"> - </td>
             <td data-title="LAST LOGIN"> - </td>
           </tr>
           ';
         }
      ?>
       </tbody>
     </table>
   </div>
  </div>
  <br>
  <br>
  <div id="demo">
    <h2>Inactive</h2>

    <!-- Responsive table starts here -->
    <!-- For correct display on small screens you must add 'data-title' to each 'td' in your table -->
    <div class="table-responsive-vertical shadow-z-1">
    <!-- Table starts here -->
    <table id="table" class="table table-hover table-mc-light-blue">
        <thead>
          <tr>
            <th>USER ID</th>
            <th>NAME</th>
            <th>EMAIL</th>
            <th>VER.CODE</th>
            <th>ACTIVE</th>
            <th>LAST LOGIN</th>
          </tr>
        </thead>
        <tbody>
       <?php
          // Begin to bring in the data of the admins: Active //
          $ADMINI = "SELECT *
                     FROM user INNER JOIN admin
                     ON user.user_id = admin.user_id
                     WHERE user.status = 2 AND user.active = 0
                     ORDER BY user.last_login DESC
                     LIMIT $page_count, 10";
          $result = $con->query($ADMINI);
          if ($result->num_rows > 0) {
           while ($row = $result->fetch_assoc()) {
             // Converting time to 12AM/PM format //
             $last_login = $row['last_login'];
             $last_login = date('d/m/y h:i A', strtotime($last_login));
             // putting together user's full name //
             $full_name =  $row['first_name']." ".$row['last_name'];
             // define the status of the account //
             if (($row['active']) == 0) {
               $status = " N ";
             } else if (($row['active']) == 1) {
               $status = " Y ";
             } else {
               $status = " - ";
             }
             echo '
             <tr>
               <td data-title="USER ID">'.$row["user_id"].'</td>
               <td data-title="NAME">'.$full_name.'</td>
               <td data-title="EMAIL">'.$row["email"].'</td>
               <td data-title="VERCODE">'.$row["verification_code"].'</td>
               <td data-title="STATUS">'.$status.'</td>
               <td data-title="LAST LOGIN">'.$last_login.'</td>
             </tr>
             ';
           }
          } else {
            echo '
            <tr>
              <td data-title="USER ID"> - </td>
              <td data-title="NAME"> - </td>
              <td data-title="EMAIL"> - </td>
              <td data-title="VERCODE"> - </td>
              <td data-title="STATUS"> - </td>
              <td data-title="LAST LOGIN"> - </td>
            </tr>
            ';
          }
        ?>
        </tbody>
      </table>
    </div>
   </div>

  <!--  Material Table Scripts-->
   <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
   <script  src="vendor/material-design-responsive-table/js/index.js"></script>

<?php
// creating the page number based on the list's number, if its over 10 records then create 1 page //
// we first find how many records in the user list //
// since we have two types of data: Active and Inactive, we have to find both of them and compare them, then take the highest value out of all //
// first we find the value of Active ones //
$TOTUSEA = "SELECT COUNT(*) AS TOT_USE_A FROM user
            WHERE active = '1' AND status = '2'";
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
           WHERE active = '0' AND status = '2'";
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
        <a class="page" onclick="admTable(this)" name="table-page" value="';
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
          echo '" onclick="admTable(this)" name="table-page" id="page-1" value="1"> 1 </a>';
 for ($n = 2; $n <= $page_numb; $n++) {
   echo '<a class="page page-number';
   // highlight the current page number if the $_GET["page"] is the current page //
   if ($page == $n) {
     echo ' active';
   }
   echo '" onclick="admTable(this)" name="table-page" id="page-'.$n.'" value="'.$n.'">'.$n.'</a>';
 }
 echo '<a class="page" name="table-page" onclick="admTable(this)" value="';
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
