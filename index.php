<?php 
    session_start();
    include("include/nctsClass.php");
    include("include/xml.php");
    date_default_timezone_set("Asia/Bangkok");
    $nctsXML = new SimpleXMLElement($xmlstr);
    $nCompany = $nctsXML->ncts[0]->genId->nCompany;;
    $channelAccessToken = $nctsXML->ncts[0]->genId->channelAccessToken;
    $channelSecret = $nctsXML->ncts[0]->genId->channelSecret;
    $liffId = $nctsXML->ncts[0]->genId->liffId;
    $page = $nctsXML->ncts[0]->genId->page;
    $firstAdmin = $nctsXML->ncts[0]->genId->firstAdmin;

    $n = new ncts("","","");
    $date = date("Y-m-d H:i:s");
    if(isset($_SESSION['iUser'])){
      $user = $_SESSION['iUser'];
    }else{
      header("location:login.php");
    }
    

?>

<!doctype html>
<html lang="en">

<head>
  <title>GameRoom</title>
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
<!-- Option 1: Include in HTML -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
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
            <img src="" class="rounded-circle" alt="" width="50px" height="50px">
            <span class="navbar-brand text-white ms-2" href="#">GameRoom</span>
        </div>
            <div class="profile mx-3 my-auto d-flex">
                <img src="" class="rounded-circle me-2" alt="" width="30px">
                <div class="dropdown">
                    <a class=" dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo $user[0]->memName." ".$user[0]->memSur;  ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-lg-end" aria-labelledby="dropdownMenuButton1">
                        <li><a class="dropdown-item" href="profile.php">โปรไฟล์</a></li>
                        <li><a class="dropdown-item" href="order.php">คำสั่งซื้อ</a></li>
                        <li><a class="dropdown-item" href="#" id="logout">ออกจากระบบ</a></li>
                    </ul>
                </div>
            </div>
  </div>
</nav>

<div class="row">
    <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar  collapse">
      <div class="position-sticky pt-3">
        <ul class="nav flex-column">
          <?php  
            $sqlMenu = "SELECT * FROM menuList WHERE nCompany = '$nCompany' AND status='$user->status' OR status='5'";
            $qMenu = $n->sel($sqlMenu);
            foreach($qMenu as $datalist){
              $sqlDetail = "SELECT * FROM menu WHERE menuId='$datalist->menuId'";
              $qDetail = $n->sel($sqlDetail);
          ?>
          <li class="nav-item mt-2">
          <a class="nav-link text-white" onclick="page('<?php echo $qDetail[0]->link; ?>')" href="#">
              <i class="bi <?php echo $qDetail[0]->emoji ?>" width="24px" height="24px"></i>
               <?php echo $qDetail[0]->menuName; ?>
            </a>
          </li>
          <?php } ?>
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
    $(document).ready(function(){
      $(document).on("click","#logout",function(){
          $.ajax({
            url:"user.logout.php",
            dataType:"json",
            success:function(data){
              Swal.fire({
                positon:"top-end",
                title:"ออกจากระบบเสร็จสิ้น",
                timer:1000,
                showConfirmButton:false,
                icon:"success"
              }).then((result) => {
                window.location.reload()
              });
            }
          });
      });
    });

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