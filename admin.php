<?php 
    include("../include/connect.inc.php");
    session_start();
    $user = $_SESSION['User'];

?>

<!doctype html>
<html lang="en">

<head>
  <title>Title</title>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS v5.2.1 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
</head>
<style>
     *{
        font-family: 'Kanit', sans-serif;
    }
    body{
        height: auto;
        background-color: #F0E7E3;
    }
    .sidebar{
        background-color: #36261F;
        height: 95vh;
    }
    .navbar{
        background-color: #36261F;
    }
    #dropdownMenuButton1{
        text-decoration: none;
        color: #ffff;
    }

</style>
<body>
<nav class="navbar navbar-expand-lg ">
  <div class="container-fluid d-flex justify-content-between my-2 my-auto">
        <div class="logo me-auto">
            <img src="../image/logo.jpg " class="rounded-circle" alt="" width="50px" height="50px">
            <span class="navbar-brand text-white ms-2" href="#">Shabu TemIm</span>
        </div>
            <div class="profile mx-3 my-auto d-flex">
                <img src="../image/user.jpg" class="rounded-circle me-2" alt="" width="30px">
                <div class="dropdown">
                    <a class=" dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo $user->username ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-lg-end" aria-labelledby="dropdownMenuButton1">
                        <li><a class="dropdown-item" href="profile.php">โปรไฟล์</a></li>
                        <li><a class="dropdown-item" href="order.php">คำสั่งซื้อ</a></li>
                        <li><a class="dropdown-item" href="" id="logout">ออกจากระบบ</a></li>
                    </ul>
                </div>
            </div>
  </div>
</nav>

<div class="row">
    <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar  collapse">
      <div class="position-sticky pt-3">
        <ul class="nav flex-column">
          <li class="nav-item mt-2">
            <a class="nav-link  text-white" aria-current="page" onclick="page(1)" href="#">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
               ออเดอร์สินค้า
            </a>
          </li>
          <li class="nav-item mt-2">
            <a class="nav-link text-white" onclick="page(2)" href="#">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file" aria-hidden="true"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
              สรุปออร์เดอร์สินค้า
            </a>
          </li>
          <li class="nav-item mt-2">
            <a class="nav-link text-white" onclick="page(3)">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart" aria-hidden="true"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
              รายการสินค้า
            </a>
          </li>
          <li class="nav-item mt-2">
            <a class="nav-link text-white" onclick="page(4)">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
              รายชื่อสมาชิก
            </a>
          </li>
        </ul>
      </div>
    </nav>
    
    <div class="col-9">
        <div id="iCen">

        </div>
    </div>
</div>
</div>

<script>
    function page(codePage){
        var formdata = new FormData();
        formdata.append("page",codePage);
        $.ajax({
            url:"page.order.php",
            type:"POST",
            data:formdata,
            dataType:"html",
            contentType:false,
            processData:false,
            success:function(data){
                $('#iCen').html(data);
            }

        })
    }
</script>
  <!-- Bootstrap JavaScript Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
    integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
    integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
  </script>
</body>

</html>