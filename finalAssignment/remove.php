<?php
  error_reporting(E_ALL);
  ini_set("display_errors",1);
  header("Content-type: application/json");
  include "password.php";
  
  $success = false;
  
  // Connect to database
  $mysqli = new mysqli("oniddb.cws.oregonstate.edu", "dhusek-db", $password, "dhusek-db");
  // Check for connection errors
  if ($mysqli->connect_errno) {
    $error = "Connection to database failed.";
  } else {
    $deleteID = $_GET["id"];
    // prepare statement
    if (!($stmt = $mysqli->prepare("DELETE FROM feedings WHERE id = ?"))) {
      $error = "Prepare failed. Database problem, please try again later.";
    } else {
      // bind statement
      if (!$stmt->bind_param("i", $id)) {
        $error = "Binding parameters failed. Database problem, please try again later.";
      } else {
        // set parameters
        $id = $deleteID;
        if (!$stmt->execute()) {
          $error = "Execute failed. Database problem, please try again later.";
        } else {
          $success = true;
          $error = "";
        }
      }
    }
  }
  
  $output = array("error"=>$error, "success"=>$success);
  echo json_encode($output);
?>