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
    $inputUsername = $_GET["username"];
    $inputPasswordHash = $_GET["passwordHash"];
    if ($inputUsername == "" || $inputUsername == NULL) {
      $error = "You must enter a user name.";
    } else {
      if (strlen($inputUsername) > 20) {
        $error = "Your user name must be no more than 20 characters.";
      } else {
        // prepare statement
        if (!($stmt = $mysqli->prepare("INSERT INTO users (username, passwordHash) VALUES(?, ?)"))) {
          $error = "Prepare failed. Database problem, please try again later.";
        } else {
          // bind statement
          if (!$stmt->bind_param("ss", $username, $passwordHash)) {
            $error = "Binding parameters failed. Database problem, please try again later.";
          } else {
            // set parameters
            $username = $inputUsername;
            $passwordHash = $inputPasswordHash;
            if (!$stmt->execute()) {
              if ($stmt->errno == 1062) {
                $error = '"' . $inputUsername . '" already exists.  You must enter a unique user name.';
              } else {
                $error = "Execute failed. Database problem, please try again later.";
              }
            } else {
              $success = true;
              $error = "";
              session_start();
              $_SESSION["username"] = $inputUsername;
            }
          }
        }
      }
    }
  }
  
  $output = array("error"=>$error, "success"=>$success);
  echo json_encode($output);
?>