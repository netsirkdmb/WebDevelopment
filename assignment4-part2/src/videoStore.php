<?php
  error_reporting(E_ALL);
  ini_set("display_errors",1);
  header("Content-type: text/html");
  include "password.php";
  
  // Connect to database
  $mysqli = new mysqli("oniddb.cws.oregonstate.edu", "dhusek-db", $password, "dhusek-db");
  // Check for connection errors
  if ($mysqli->connect_errno) {
    echo "Connection to MySQL failed: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    exit;
  }
  
  // Rent a movie
  if(isset($_GET["action"]) && $_GET["action"] == "Rent") {
    // prepare statement
    if (!($stmt = $mysqli->prepare("UPDATE videos SET rented = 1 WHERE id = ?"))) {
      echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
  // Return an movie
  } else if (isset($_GET["action"]) && $_GET["action"] == "Return"){
    // prepare statement
    if (!($stmt = $mysqli->prepare("UPDATE videos SET rented = 0 WHERE id = ?"))) {
      echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
  // Delete a movie
  } else if (isset($_GET["action"]) && $_GET["action"] == "delete"){
    // prepare statement
    if (!($stmt = $mysqli->prepare("DELETE FROM videos WHERE id = ?"))) {
      echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
  }
  // Bind and set parameters from prepared statements above
  // bind parameters
  if (isset($_GET["action"]) && ($_GET["action"] == "Rent" || $_GET["action"] == "Return" || $_GET["action"] == "delete")) {
    if (!$stmt->bind_param("i", $id)) {
      echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    }
    // set parameters
    $id = $_GET["id"];
  }
  
  // Add a movie
  if (isset($_POST["action"]) && $_POST["action"] == "add"){
    $userName = $_POST["name"];
    $userCategory = $_POST["category"];
    $userLength = $_POST["length"];
    // variable to check if bad information has been entered
    $isError=false;
    // error checking
    if ($userName == "" || $userName == NULL) {
      echo "You must enter a video name.<br>";
      $isError=true;
    }
    if (strlen($userName) > 255) {
      echo "Video Name entry must be no more than 255 characters.<br>";
      $isError=true;
    }
    if (strlen($userCategory) > 255) {
      echo "Category entry must be no more than 255 characters.<br>";
      $isError=true;
    }
    if ($userCategory == "" || $userCategory == NULL) {
      $userCategory = "N/A";
    }
    if ($userLength == "" || $userLength == NULL) {
      $userLength = 1;
    }
    if (!is_numeric($userLength) || intval($userLength) != floatval($userLength) || intval($userLength) < 0) {
      echo "You must enter a positive integer for the length of the video.<br>";
      $isError=true;
    }
    // prepare statement
    if (!($stmt = $mysqli->prepare("INSERT INTO videos (name, category, length, rented) VALUES(?, ?, ?, ?)"))) {
      echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    // bind statement
    if (!$stmt->bind_param("ssii", $name, $category, $length, $rented)) {
      echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    }
    // set parameters
    $name = $userName;
    $category = $userCategory;
    $length = $userLength;
    $rented = 0;
  }
  
  // Execute prepared statements from above
  if ((isset($_GET["action"]) && ($_GET["action"] == "Rent" || $_GET["action"] == "Return" || $_GET["action"] == "delete")) || 
      (isset($_POST["action"]) && $_POST["action"] == "add" && !$isError)) {
    if (!$stmt->execute()) {
      if ($stmt->errno == 1062) {
        echo $_POST["name"] . " already exists in the video inventory.  You must enter a unique video name.<br>";
      } else {
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
      }
    }
    $stmt->close();
  }
  
  // Delete all movies
  // Delete videos table if it exists and create table again.  Print error message if either action fails.
  if (isset($_GET["action"]) && $_GET["action"] == "deleteAll") {
    // delete table if it exists and create table again.
    if (!$mysqli->query("DROP TABLE IF EXISTS videos") || 
      !$mysqli->query("CREATE TABLE videos(
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) UNIQUE NOT NULL,
        category VARCHAR(255),
        length INT UNSIGNED,
        rented TINYINT(1) NOT NULL)")) { // rented defaults to 0, which means the video is available
    echo "Failed to create table: (" . $mysqli->errno . ") " . $mysqli->error;
    }
  }
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Kristen's Video Store</title>
<style>
table{
  border: thin solid black;
  border-collapse: collapse;

}
th, td{
  border: thin solid black;
  text-align: center;
  padding: 5px;
}
.no-grid td{
  border: none;
  text-align: left;
}
</style>
</head>
<body>
<h1>Kristen's Video Store<br></h1>
<b>Add a video to the inventory:</b>
<form action="videoStore.php" method="post">
  <table class="no-grid">
  <tbody>
    <tr><td>Video Name: <td><input type="text" name="name" maxlength="255" required>
    <tr><td>Category: <td><input type="text" name="category" maxlength="255">
    <tr><td>Length: <td><input type="number" name="length" min="1">
    <tr><td colspan="2"><input type="hidden" name="action" value="add">
    <tr><td colspan="2"><input type="submit" value="Add Video" style="width: 100%">
  </table>
</form>
<br>
<br>
<form action="videoStore.php" method="post">
   Filter by Category: 
   <select name="filter">
    <option value="allMovies">All Movies</option>
    <?php
      if (!($stmt = $mysqli->query("SELECT DISTINCT category FROM videos"))) {
        echo "Query failed: (" . $mysqli->errno . ") ". $mysqli->error;
      }
      while ($row = mysqli_fetch_array($stmt)) {
        echo '<option value="' . $row["category"] . '" ';
        // if categories are filtered, make dropdown menu show filtered category
        if (isset($_POST["filter"]) && $_POST["filter"] == $row["category"]) {
          echo 'selected';
        }
        echo '>' . $row["category"] . '</option>';
      }
    ?>
   </select>
   <input type="submit" value="Filter">
</form>
<br>
<br>
<table>
<caption><b>Video Inventory</b></caption>
<thead>
<tr><th colspan="2">Rental Status<th>Name<th>Category<th>Length<th>Delete Video
<tbody>
<?php
  if (isset($_POST["filter"]) && $_POST["filter"] != "allMovies") {
    // prepare statement
    if (!($stmt = $mysqli->prepare("SELECT * FROM videos WHERE category=? ORDER BY id"))) {
      echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    // bind statement
    if (!$stmt->bind_param("s", $category)) {
      echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    }
    // set parameters
    $category = $_POST["filter"];
  } else {
    // prepare statement
    if (!($stmt = $mysqli->prepare("SELECT * FROM videos ORDER BY id"))) {
      echo "Prepare failed: (" . $mysqli->errno . ") ". $mysqli->error;
    }
  }
  // bind results from above
  if (!$stmt->bind_result($id, $name, $category, $length, $rented)) {
    echo "Bind result failed: (" . $stmt->errno . ") " . $stmt->error;
  }
  // execute statements from above
  if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
  }
  while ($stmt->fetch()) {
    echo "<tr>";
    echo "<td>";
    if ($rented == 0) {
      $action = "Rent";
      $rentalText = "Available";
    } else {
      $action = "Return";
      $rentalText = "Checked out";
    }
    echo '<a href="videoStore.php?action=' . $action . '&id=' . $id . '"><button>' . $action . '</button></a>';
    echo "<td>" . $rentalText;
    echo "<td>" . $name;
    echo "<td>" . $category;
    echo "<td>" . $length;
    echo '<td><a href="videoStore.php?action=delete&id=' . $id . '"><button>Delete</button></a>';
  }
  $stmt->close();
?>
</table>
<br>
<br>
<a href="videoStore.php?action=deleteAll"><button>Delete ALL Videos!</button></a>
<?php
  $mysqli->close(); 
?>
</body>
</html>