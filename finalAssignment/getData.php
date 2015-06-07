<?php
  error_reporting(E_ALL);
  ini_set("display_errors",1);
  header("Content-type: application/json");
  include "password.php";
  
  $success = false;
  $data = array();
  
  session_start();
  if(isset($_SESSION["username"])) {
    $sessionUsername = $_SESSION["username"];
  } else {
    echo json_encode(array());
    exit;
  }
    
  // Connect to database
  $mysqli = new mysqli("oniddb.cws.oregonstate.edu", "dhusek-db", $password, "dhusek-db");
  // Check for connection errors
  if ($mysqli->connect_errno) {
    $error = "Connection to database failed.";
  } else {
    // prepare statement
    if (!($stmt = $mysqli->prepare("SELECT * FROM feedings WHERE user=? ORDER BY createdAt, time"))) {
      $error = "Prepare failed. Database problem, please try again later.";
    } else {
      // bind statement
      if (!$stmt->bind_param("s", $user)) {
        $error = "Binding parameters failed. Database problem, please try again later.";
      } else {
        // set parameters
        $user = $sessionUsername;
        if (!$stmt->execute()) {
          $error = "Execute failed. Database problem, please try again later.";
        } else {
          $result = $stmt->get_result();
          while($row = $result->fetch_array(MYSQLI_ASSOC)) {
            array_push($data, $row); 
          };
          $success = true;
          $error = "";
        }
      }
    }
  }
  
  $output = array("error"=>$error, "success"=>$success, "data"=>$data);
  echo json_encode($output);
?>