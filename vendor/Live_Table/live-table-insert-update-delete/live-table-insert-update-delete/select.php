<?php
 $connect = mysqli_connect("localhost", "root", "", "testing");
 $output = '';
 $sql = "SELECT * FROM tbl_sample ORDER BY id DESC";
 $result = mysqli_query($connect, $sql);
 $output .= '
      <div class="">  
           <table class="table table-bordered">
                <tr>
                     <th width="10%">Id</th>
                     <th width="40%">First Name</th>
                     <th width="40%">Last Name</th>
                     <th width="10%">Delete</th>
                </tr>';
 $rows = mysqli_num_rows($result);
 if($rows > 0)
 {
	  if($rows > 10)
	  {
		  $delete_records = $rows - 10;
		  $delete_sql = "DELETE FROM tbl_sample LIMIT $delete_records";
		  mysqli_query($connect, $delete_sql);
	  }
      while($row = mysqli_fetch_array($result))
      {
           $output .= '
                <tr>
                     <td>'.$row["id"].'</td>
                     <td class="first_name" data-id1="'.$row["id"].'" contenteditable>'.$row["first_name"].'</td>
                     <td class="last_name" data-id2="'.$row["id"].'" contenteditable>'.$row["last_name"].'</td>
                     <td><button type="button" name="delete_btn" data-id3="'.$row["id"].'" class="btn btn-xs btn-danger btn_delete">x</button></td>
                </tr>
           ';
      }
      $output .= '
           <tr>
                <td></td>
                <td id="first_name" contenteditable></td>
                <td id="last_name" contenteditable></td>
                <td><button type="button" name="btn_add" id="btn_add" class="btn btn-xs btn-success">+</button></td>
           </tr>
      ';
 }
 else
 {
      $output .= '
				<tr>
					<td></td>
					<td id="first_name" contenteditable></td>
					<td id="last_name" contenteditable></td>
					<td><button type="button" name="btn_add" id="btn_add" class="btn btn-xs btn-success">+</button></td>
			   </tr>';
 }
 $output .= '</table>
      </div>';
 echo $output;
 ?>
