<?php
session_start();

if(isset ($_SESSION["userID"]) && $_SESSION["aStatus"] == 1){
    header("Location: uraDashboard.php");
    return;
}

if(isset ($_SESSION["userID"])){
  header("Location: dashboard.php");
  return;
}
?>
<!DOCTYPE html>
<head>
  <title>Flourishing Our Locale: Login</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <!-- Required meta tags -->
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://fonts.googleapis.com/css2?family=Satisfy&display=swap" rel="stylesheet">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
    integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" />

  <!-- Internal CSS -->
  <style>
    .btn-success {
      background-color: #42B72A;
      border: none;
    }

    body {
      background-color: #F0F2F5;
      background-image: url("images/FlourishingOurLocaleXXs.jpg");
      background-size:cover;                   
      background-repeat: no-repeat;
      background-position: center 40%;   
      overflow-y: hidden; 
      overflow-x: hidden; 
    }

    .loginContainer {
      border-radius: 10px;
      background-color: rgba(255, 255, 255, 0.753);
      padding: 15px;
      margin: 15px;
    }

    .loginContainer img {
      width: 50px;
    }

    .row {
      height: 100vh;
    }

    .titleColor{
        color:#5a5a5ae0;
        font-family: 'Satisfy', cursive;
        font-size: 2.5em;
    }
  </style>
</head>

<body>
  <div class="row align-items-center">
    <!-- Main container -->
    <div class="container col-10 col-sm-6 col-md-6 col-lg-4 p-5 shadow-lg mx-auto d-block loginContainer">
      <!-- Vessel Image -->

      <p class="text-center titleColor">Flourishing Our Locale</p>


      <!-- Username Field -->
      <div class="form-group">
        <input type="username" name="username" id="username" class="form-control" placeholder="Email Address" required>
      </div>

      <!-- Password Field -->
      <div class="form-group">
        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
      </div>

      <!-- Form Success/Failure Messages -->
      <div th:if="${formError != null}" th:text="${formError}" class="form-group" style="color: red;"></div>
      <div th:if="${formSuccess != null}" th:text="${formSuccess}" class="form-group" style="color: green;"></div>

      <!-- Login Button -->
      <button id="loginBtn" type="submit" onclick="submitLoginRequest()" class="btn btn-primary btn-block">Login</button>

      <!-- Error Messages -->
      <div id="errorMsg" class="float-left" style="color: red;" ></div>
      <br>
      <!-- Forgot Password Link -->
      <a href="">Forgot Password?</a>

      <!-- Seperator -->
      <hr style="margin-top:0px" >

      <!-- Register Button -->
      <button class="btn btn-success">
        <a href="register.html" class="text-decoration-none text-white d-block">Create New Account</a>
      </button>

    </div>
  </div>

<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

<!-- Load common.js from scripts folder -->
<script src="scripts/common.js"></script>

<script>

  $('#password,#username').keyup(function () {
    if ($('#username').val() && $('#password').val()) {
      const errorMsg = document.getElementById("errorMsg");
      errorMsg.innerText = "";
              
      if($('#username').val() == ""){
        errorMsg.innerText = "Username cannot be emty";
        document.getElementById("loginBtn").disabled = true;
      }
      else if($('#password').val()==""){
        errorMsg.innerText = "Password cannot be empty";
        document.getElementById("loginBtn").disabled = true;
      }
      else{
        document.getElementById("loginBtn").disabled = false;
      }
    }
  });

  async function submitLoginRequest(){
    let name = "";
    let store_id = "";
    let user_id = "";

    if($('#username').val() == ""){
      errorMsg.innerText = "Username cannot be emty";
      document.getElementById("loginBtn").disabled = true;
    }
    else if($('#password').val()==""){
      errorMsg.innerText = "Password cannot be empty";
      document.getElementById("loginBtn").disabled = true;
    }
    else{
      let requestParameters = "email=" + encodeURIComponent($('#username').val()) +
                              "&password=" + encodeURIComponent($('#password').val());
  
      let creationResponse  = await makeRequestxwwwFormURLEncode(hostname + "/users/login", "POST", requestParameters);
      let responseMsg = JSON.parse(creationResponse)['response'];
      const errorMsg = document.getElementById("errorMsg");
      errorMsg.innerText = "";
      console.log(responseMsg);

      if(responseMsg == "Incorrect Password" || responseMsg == "User not found"){
        errorMsg.innerText = "Incorrect Username or Password";
      }else if(responseMsg == "Server Error"){
          errorMsg.innerText = "Server Error Please Try Again Soon";
      }else if(responseMsg == true){
        let name     = JSON.parse(creationResponse)['name'];
        let store_id = JSON.parse(creationResponse)['store_id'];
        let user_id  = JSON.parse(creationResponse)['user_id']
        let admin    = JSON.parse(creationResponse)['admin'];
   
        let url = 'processLogin.php';
        let form = $(
          `<form action="${url}" method="POST">
            <input type="text" name="name" value="${name}"/>
            <input type="text" name="storeID" value="${store_id}"/>
            <input type="text" name="aStatus" value="${admin}"/>
            <input type="text" name="userID" value="${user_id}"/>
          </form>`);
        $('body').append(form);
        form.submit();
      }
    }
  }
  document.getElementById("loginBtn").disabled = true;

  var input = document.getElementById("password");
  input.addEventListener("keyup", function(event) {
    if (event.keyCode === 13) {
    event.preventDefault();
    document.getElementById("loginBtn").click();
    }
  });

</script>
</body>

</html>