<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Bottle Feeding Log</title>
<!--jQuery-->
  <script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<!--Bootstrap-->
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
  <!-- Optional theme -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
  <!-- Latest compiled and minified JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="index.css">
<!--password hashing: https://github.com/placemarker/jQuery-MD5-->
  <script src="jquery.md5.js"></script>
<!--MomentJS-->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.min.js"></script>
</head>
<body>
  <!--Bootstrap signin form: http://getbootstrap.com/examples/signin/ -->
  <div class="container" id="login">
    <form class="form-signin">
      <h2 class="form-signin-heading">Please sign in</h2>
      <label for="username" class="sr-only">User Name</label>
      <input type="text" id="username" class="form-control" placeholder="User Name" maxlength="20" required autofocus>
      <label for="inputPassword" class="sr-only">Password</label>
      <input type="password" id="inputPassword" class="form-control" placeholder="Password" required>
      <button class="btn btn-lg btn-primary btn-block" type="submit" id="signIn">Sign in</button>
      <button class="btn btn-lg btn-default btn-block type="submit" id="createAccount">Create Account</button>
    </form>
  </div>
  
  <div class="container clearfix" id="logout">
    <div class="pull-right">
      <h3>Hi, <span id="currentUser"></span><button class="btn btn-lg btn-danger" id="signOut">Logout</button></h3>
    </div>
  </div>
  
  <div id="feedingLog">
    <h1>Bottle Feeding Log</h1>
    <br>
    <div class="row">
      <div class="col-md-4">
        <button type="button" class="btn btn-success btn-lg" id="addButton">Add Feeding</button>
        <div id="add">
          <form class="form-signin">
            <h2 class="form-signin-heading">Add a feeding</h2>
            <label for="time" class="sr-only">Time</label>
            <input type="time" id="addTime" class="form-control" required autofocus>
            <label for="amount" class="sr-only">Amount (oz)</label>
            <input type="number" id="addAmount" class="form-control" min="0.5" step="0.5" onkeypress="return isNumberKey(event)" required>
            <button class="btn btn-lg btn-primary" id="addFeeding">Add Feeding</button>
            <button class="btn btn-lg btn-warning pull-right" id="cancelAdd">Cancel</button>
          </form>
        </div>
      </div>
      <div class="col-md-4"></div>
      <div class="col-md-4">
        <div id="edit">
          <form class="form-signin">
            <h2 class="form-signin-heading">Edit a feeding</h2>
            <label for="time" class="sr-only">Time</label>
            <input type="time" id="editTime" class="form-control" required autofocus>
            <label for="amount" class="sr-only">Amount (oz)</label>
            <input type="number" id="editAmount" class="form-control" placeholder="0.5" min="0.5" step="0.5" onkeypress="return isNumberKey(event)" required>
            <button class="btn btn-lg btn-primary" id="saveFeeding">Save Feeding</button>
            <button class="btn btn-lg btn-warning pull-right" id="cancelEdit">Cancel</button>
          </form>
        </div>
      </div>
    </div>
    <br>
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <table class="table table-bordered" id="feedingTable">
          <thead>
            <tr><th>Date</th><th>Time</th><th>Amount (oz)</th><th colspan="2">Edit</th></tr>
          </thead>
          <tbody>
            
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
<script>
  function checkLoggedIn() {
    $.getJSON("isUserLoggedIn.php").done(
      function(data) {
        if(data["logged_in"]) {
          $("#login").hide();
          $("#logout").show();
          $("#feedingLog").show();
          $("#addButton").show();
          $("#currentUser").text(data["username"]);
        } else {
          $("#login").show();
          $("#logout").hide();
          $("#feedingLog").hide();
          $("#addButton").hide();
        }
      } 
    );
  }
  function clickEditButton(event) {
    event.preventDefault();
    $("#editTime").val(event.target.attributes.time.value);
    $("#editAmount").val(event.target.attributes.amount.value);
    $("#edit").show();
    $("#add").hide();
    $("#saveFeeding").attr("rowID", event.target.attributes.rowID.value);
  }
  function clickDeleteButton(event) {
    event.preventDefault();
    var id = event.target.attributes.rowID.value;
    $.getJSON("remove.php", {id: id}).done( function() {
      refreshTable();
    });
  }
  function refreshTable() {
    $.getJSON("getData.php").done(
      function(feedingData) {
        var theDate;
        $("#feedingTable tbody").empty();
        if(feedingData["success"]) {
          feedingData["data"].forEach( function(row) {
            theDate = moment(row["createdAt"]);
            var hour = row["time"].split(":")[0];
            var minute = row["time"].split(":")[1];
            theDate.hour(hour);
            theDate.minute(minute);
            var htmlRow = "<tr><td>" + theDate.format("MM/DD/YYYY") + "</td>";
            htmlRow += ("<td>" + theDate.format("h:mm A") + "</td>");
            htmlRow += ("<td>" + row["amount"] + "</td>");
            htmlRow += ("<td><button class='editButton btn btn-primary' rowID='" + row["id"] + "' time='" + row["time"] + "' amount='" + row["amount"] + "'>Edit</button>");
            htmlRow += ("<button class='deleteButton btn btn-danger' rowID='" + row["id"] + "'>Delete</button></td>");
            htmlRow += ("</td></tr>");
            $("#feedingTable tbody").append(htmlRow);
          });
          $(".editButton").click(clickEditButton);
          $(".deleteButton").click(clickDeleteButton);
        }
      }
    );
  }
  function isNumberKey(e) {
    var charCode = (e.which) ? e.which : event.keyCode;
    if (charCode > 31 && (charCode < 46 || charCode > 57 || charCode == 47)) {
      return false;
    }
    return true;
  }
  $("#signIn").click( function(event) {
    var username;
    var passwordHash;
    username = $("#username").val();
    passwordHash = $.md5($("#inputPassword").val());
    $.getJSON("login.php", {"username":username, "passwordHash":passwordHash}).done(
      function(data) {
        if(!data["success"]) {
          alert(data["error"]);
        }
        checkLoggedIn();
        refreshTable();
      }
    );
    event.preventDefault();
  });
  $("#createAccount").click( function(event) {
    event.preventDefault();
    var username;
    var password;
    var passwordHash;
    username = $("#username").val();
    password = $("#inputPassword").val();
    if (password.length < 8) {
      alert("Your password must be at least 8 characters.");
      return;
    }
    passwordHash = $.md5(password);
    $.getJSON("createUser.php", {"username":username, "passwordHash":passwordHash}).done(
      function(data) {
        if(!data["success"]) {
          alert(data["error"]);
        }
      }
    );
    checkLoggedIn();
  });
  $("#addButton").click( function(event) {
    event.preventDefault();
    $("#addTime").val("");
    $("#addAmount").val("");
    $("#add").show();
    $("#edit").hide();
    $("#addButton").hide();
  });
  $("#addFeeding").click( function(event) {
    event.preventDefault();
    $("#add").hide();
    $("#addButton").show();
    var time = $("#addTime").val();
    var amount = $("#addAmount").val();
    $.getJSON("add.php", {time: time, amount: amount}).done( function(data) {
      if (!data["success"]) {
        alert(data["error"]);
      }
      refreshTable();
    });
  });
  $("#cancelAdd").click( function(event) {
    event.preventDefault();
    $("#add").hide();
    $("#addButton").show();
  });
  $("#saveFeeding").click( function(event) {
    event.preventDefault();
    $("#edit").hide();
    var time = $("#editTime").val();
    var amount = $("#editAmount").val();
    var id = event.target.attributes.rowID.value;
    $.getJSON("edit.php", {time: time, amount: amount, id: id}).done( function(data) {
      if (!data["success"]) {
        alert(data["error"]);
      }
      refreshTable();
    });
  });
  $("#cancelEdit").click( function(event) {
    event.preventDefault();
    $("#edit").hide();
  });
  $("#logout").click( function(event) {
    $.ajax("logout.php").done(
      function() {
        $("#username").val("");
        $("#inputPassword").val("");
        checkLoggedIn();
      }
    );
    event.preventDefault();
  });
  
  $(document).ready( function() {
    checkLoggedIn();
    refreshTable();
  });
</script>
</html>