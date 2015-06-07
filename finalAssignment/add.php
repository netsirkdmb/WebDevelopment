<?php
  error_reporting(E_ALL);
  ini_set("display_errors",1);
  header("Content-type: application/json");
  include "password.php";
  
  $success = false;
  
  session_start();
  if(isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
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
    $inputTime = $_GET["time"];
    $inputAmount = $_GET["amount"];
    $today = date("Y-m-d");
    // check time input
    $timeOK = true;
    if (strpos($inputTime, ":")) {
      list($hour, $minute) = explode(":", $inputTime);
      if ($hour < 0 || $hour > 23) {
        $timeOK = false;
      }
      if ($minute < 0 || $minute > 59) {
        $timeOK = false;
      }
    } else {
      $timeOK = false;
    }
    if (!$timeOK) {
      $error = "Invalid time.";
    } else {
      // check amount input
      if ($inputAmount < 0.5 || (($inputAmount * 10) % 5) != 0) {
        $error = "Invalid amount.  The amount must be given in 0.5 ounce increments with a minimum of 0.5 ounces.";
      } else {
        // prepare statement
        if (!($stmt = $mysqli->prepare("INSERT INTO feedings (time, amount, user, createdAt) VALUES(?, ?, ?, ?)"))) {
          $error = "Prepare failed. Database problem, please try again later.";
        } else {
          // bind statement
          if (!$stmt->bind_param("sdss", $time, $amount, $user, $createdAt)) {
            $error = "Binding parameters failed. Database problem, please try again later.";
          } else {
            // set parameters
            $time = $inputTime;
            $amount = $inputAmount;
            $user = $username;
            $createdAt = $today;
            if (!$stmt->execute()) {
              $error = "Execute failed. Database problem, please try again later.";
            } else {
              $success = true;
              $error = "";
            }
          }
        }
      }
    }
  }
  
  $output = array("error"=>$error, "success"=>$success);
  echo json_encode($output);
?>