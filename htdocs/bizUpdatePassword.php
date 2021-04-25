<?php
require_once  'include/common.php';
?>
<!Doctype html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>Reset Password</title>

    <!-- Load common.js from scripts folder -->
    <script src="scripts/common.js"></script>

    <!-- Load common.css from scripts folder -->
    <link rel="stylesheet" media="all" href="css/common.css">

    <!-- Material Design (External) -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Bootstrap CSS (External) -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <!-- Load fonts from google fonts (External) -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Merienda&family=Open+Sans:wght@300;600&family=Satisfy&display=swap" rel="stylesheet">
    <style>
       @media screen and (max-width : 1920px){
            .mobile-navbar{
                display:none;
            }
        }
            
        @media screen and (max-width : 767px){
            .desktop-navbar{
                display:none;
            }
            .mobile-navbar{
                display:inline;
            }
        }
    </style>
  </head>

  <body>
    <!-- NavBar -->
    <?php require_once  'bizNavBar.php' ?>

    <!-- Contents get populated from updatePasswordBody.php -->
    <?php  require_once  'updatePasswordBody.php' ?>
   
    
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <!-- Leave this below as it requires jquery -->
    <script src="scripts/updatePassword.js"></script>

    <script>
      let shopName =sessionStorage.getItem('shopName');

      let elements = document.getElementsByClassName('shopName');
      for (index in elements){
          elements[index].innerText = shopName
      }
    </script>
  </body>
</html>