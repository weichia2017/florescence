<?php
// if( isset($_SESSION["name"]) ){
//     $userName = $_SESSION["name"];
// }
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
      <a id="navBarBrand"><span style="font-family: 'Satisfy', cursive;font-size: 1.3em;">Flourishing Our Locale : </span><span class="shopName"></span></a>
      <ul class="navbar-nav mr-auto mt-2 mt-lg-0"> 
        <!-- <li class="nav-item active">
          <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Link</a>
        </li>
        <li class="nav-item">
          <a class="nav-link disabled" href="#">Disabled</a>
        </li> -->
      </ul> 
      <!-- <form class="form-inline my-2 my-lg-0">
        <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
      </form> -->

      <!-- ======================== -->
      <!-- ONLY SHOW IN MOBILE VIEW -->
      <!-- ======================== -->
      <ul class="navbar-nav mr-auto mobile-navbar">

         <!-- Dashboard Tab -->
         <li class="nav-item">
              <a class="nav-link" href="dashboard.php">
                Dashboard
              </a>
          </li>
        
          <!-- Change Password Tab -->
          <li class="nav-item">
              <a class="nav-link" href="bizUpdatePassword.php">
                Change Password
              </a>
          </li>

          <!-- Logout Tab -->
          <li class="nav-item">
              <a class="nav-link" href="processLogout.php">
                Logout
              </a>
          </li>
      </ul>

      <!-- =========================== -->
      <!--  ONLY SHOW IN DESKTOP VIEW  -->
      <!-- =========================== -->
      <ul class="navbar-nav mr-2 desktop-navbar">
        <!-- Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Account
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow-lg" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="dashboard.php">Dashboard</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="bizUpdatePassword.php">Change Password</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="processLogout.php">Logout</a>
            </div>
        </li>
      </ul>
    </div>
  </nav>