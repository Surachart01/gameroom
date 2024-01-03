<?php
session_start();
include('include/nctsClass.php');
include('include/xml.php');

date_default_timezone_set("Asia/Bangkok");
$nctsXML = new SimpleXMLElement($xmlstr);
$nCompany = $nctsXML->ncts[0]->genId->nCompany;;
$channelAccessToken = $nctsXML->ncts[0]->genId->channelAccessToken;
$channelSecret = $nctsXML->ncts[0]->genId->channelSecret;
$liffId = $nctsXML->ncts[0]->genId->liffId;
$page = $nctsXML->ncts[0]->genId->page;
$firstAdmin = $nctsXML->ncts[0]->genId->firstAdmin;
$n = new ncts($channelAccessToken,$channelSecret,$nCompany);
$u = json_decode($_SESSION['iUser']);
$status = $u[0]->status;
$aId=$user[0]->memId;
$today = date("Y-m-d");
$cache=rand(1,1000);
if(isset($_SESSION['iUser'])){
    $user = $_SESSION['iUser'];
  }else{
    header("location:login.php");
  }
?>
<!DOCTYPE html>
<html lang="th">
<head>
<style>
.modal {
    display:    none;
    position:   fixed;
    z-index:    1000;
    top:        0;
    left:       0;
    height:     100%;
    width:      100%;
    background: rgba( 255, 255, 255, .8 ) 
                url('images/loading.gif') 
                50% 50% 
                no-repeat;
    background-size: 50px 50px;
}

body.loading .modal{
    overflow: hidden;   
}

body.loading .modal{
    display: block;
}

.modal2{
    display:    none;
    position:   fixed;
    z-index:    1000;
    top:        0;
    left:       0;
    height:     100%;
    width:      100%;

}
body.loading1 .modal2 {
    overflow: hidden;   
}

body.loading1 .modal2 {
    display: block;
}
.ct{
    position: fixed;
    top: 50%;
    left: 50%;
    width:90%;
    height:90%;
    transform: translate(-50%, -50%);
    overflow: scroll;
}
.closeModal{
  position: absolute;
  right: 10px;
  font-size:18px;
  cursor:pointer;
  color:red;
}
.inpSearch{
    background: rgba(255,255,255,0.1) !important;
    border:none;
    border-bottom: 1px solid #000000;
    padding: 5px 10px;
    outline: none;
}



</style>
    <title><?php $companyTitle; ?></title>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <meta name="description" content="<?php echo $companyDesc ;?>">
      <meta name="keywords" content="<?php echo $companyKey ;?>">
      <meta name="author" content="CodedThemes">
      <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
      <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
      <link rel="stylesheet" type="text/css" href="assets/icon/themify-icons/themify-icons.css">
      <link rel="stylesheet" type="text/css" href="assets/icon/icofont/css/icofont.css">
      <link rel="stylesheet" type="text/css" href="assets/css/style.css">
      <link rel="stylesheet" type="text/css" href="assets/css/jquery.mCustomScrollbar.css">
      <script type="text/javascript" src="js/jquery-1.8.2.min.js"></script>
      <script type="text/javascript" src="js/jquery-1.8.2.min.js?cache=<?php echo $cache;?>"></script>
        <script type="text/javascript" src="assets/js/jquery-ui/jquery-ui.min.js?cache=<?php echo $cache;?>"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
            integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
            integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
        </script>
        <script type="text/javascript" src="assets/js/script.js?cache=<?php echo $cache;?>"></script>
        <script src="assets/js/pcoded.min.js?cache=<?php echo $cache;?>"></script>
        <script src="assets/js/demo-12.js?cache=<?php echo $cache;?>"></script>
        <script src="assets/js/jquery.mCustomScrollbar.concat.min.js?cache=<?php echo $cache;?>"></script>
        <script src="js/all.js?cache=<?php echo $cache;?>"></script>
        <script type="text/javascript" src="js/jquery.address-1.5.min.js?tracker=track&cache=<?php echo $cache;?>"></script>
        <script type="text/javascript" src="js/jquery-ui.js?cache=<?php echo $cache;?>"></script>
        <script charset="utf-8" src="https://static.line-scdn.net/liff/edge/2/sdk.js?cache=<?php echo $cache;?>"></script>
        <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js?cache=<?php echo $cache;?>"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://static.line-scdn.net/liff/edge/2.1/sdk.js"></script>
  </head>


  <script>
     $(document).ready(function(){

        var url = window.location.search;
        var get = new URLSearchParams(url);
        var codePage = get.get('page');
        page(codePage);

        $(document).on("click","#iLogout",function(){
            liff.init({ liffId: "2000369709-DxRo3W7Z" }).then((result) => {
                liff.logout();
            }).catch((err) => {
                console.log("error");
            });
            
          $.ajax({
            url:"user.logout.php",
            dataType:"text",
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

        $.ajax({
            url:"page.order.php",
            type:"POST",
            data:{"page":codePage},
            dataType:"html",
            success:function(data){
                $('#iCen').html(data);
            }

        });
    }
  </script>
<body>
    <!-- Pre-loader start -->
    <div class="theme-loader">
        <div class="ball-scale">
            <div class='contain'>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">

                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pre-loader end -->
    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">

            <nav class="navbar header-navbar pcoded-header">
                <div class="navbar-wrapper">

                    <div class="navbar-logo">
                        <a class="mobile-menu" id="mobile-collapse" href="#!">
                            <i class="ti-menu"></i>
                        </a>
                        <a class="mobile-search morphsearch-search" href="#">
                            <i class="ti-search"></i>
                        </a>
                        <a href="index.php">
                            <div class="d-flex my-auto">
                                <img  class="img-fluid f-left me-3" src="img/bitnami.ico" width="30px" alt="Theme-Logo" />
                                 <?php echo '<span class="p-t-10">'."gameroom".'</span>'; ?>
                        </div>
                            
                        </a>
                        <a class="mobile-options">
                            <i class="ti-more"></i>
                        </a>
                    </div>

                    <div class="navbar-container container-fluid">
                        <ul class="nav-left">
                            <li>
                                <div class="sidebar_toggle"><a href="javascript:void(0)"><i class="ti-menu"></i></a></div>
                            </li>

                            <li>
                                <a href="#!" onclick="javascript:toggleFullScreen()">
                                    <i class="ti-fullscreen"></i>
                                </a>
                            </li>
                        </ul>
                        <ul class="nav-right">
                            <li class="user-profile header-notification">
                                <a href="#" onclick="$.address.value('/profile'); return false;" id="profile">
                                    <img class="img-radius rounded-circle" id="profileL" src="<?php echo $user[0]->memPic; ?>" alt="User-Profile-Image">
                                    <span id="nameL"><?php echo $user[0]->nickName;?></span>
                                    <i class="ti-angle-down"></i>
                                </a>
                                <ul class="show-notification profile-notification">
                                    
                                    <li>
                                    <a href="#"  onclick="return false;" class="editProfile" data-param="<?php echo $aId;  ?>" ><i class="ti-user"></i>View Profile</a>
                                    </li>
                                    
                                    <li>
                                        <a href="#"  id="iLogout">
                                            <i class="ti-layout-sidebar-left"></i> Logout
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="modal"><!-- Place at bottom of page --></div>
                <div class="modal2"><!-- Place at bottom of page --></div>
            </nav>
            <div class="pcoded-main-container">
                <div class="pcoded-wrapper">
                    <nav class="pcoded-navbar">
                        <div class="sidebar_toggle"><a href="#"><i class="icon-close icons"></i></a></div>
                        <div class="pcoded-inner-navbar main-menu">
                            <div class="">
                                <div class="main-menu-header">
                                    <img class="img-40 img-radius rounded-circle" id="profileR" src="<?php echo $user[0]->memPic; ?>" alt="User-Profile-Image">
                                    <div class="user-details">
                                        <span id="nameR"><?php echo $user[0]->nickName;?></span>
                                        <span id="more-details">Setting<i class="ti-angle-down"></i></span>
                                    </div>
                                </div>

                                <div class="main-menu-content">
                                    <ul>
                                        <li class="more-details">
                                            <a href="#"  onclick="return false;" class="editProfile" data-param="<?php echo $aId;  ?>" ><i class="ti-user"></i>View Profile</a>
                                            <a href="#" ><i class="ti-settings"></i>Settings</a>
                                            <a href="#"  id="iLogout"><i class="ti-layout-sidebar-left"></i>Logout</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            

                            <div class="pcoded-navigatio-lavel" data-i18n="nav.category.forms">เมนูหลัก</div>
                            <ul class="pcoded-item pcoded-left-item">
                            <?php  
                                $sqlMenu = "SELECT * FROM menuList WHERE nCompany = '$nCompany' AND status='$user->status' OR status='5'";
                                $qMenu = $n->sel($sqlMenu);
                                foreach($qMenu as $datalist){
                                $sqlDetail = "SELECT * FROM menu WHERE menuId='$datalist->menuId'";
                                $qDetail = $n->sel($sqlDetail);
                            ?>
                                <li>
                                    <a  onclick="window.location.href = '?page=gameRoom'; return false;" id="chooseMember">
                                        <span class="pcoded-micon"><i class="<?php echo $qDetail[0]->emoji; ?>"></i><b>FC</b></span>
                                        <span class="pcoded-mtext" data-i18n="nav.form-components.main"><?php echo $qDetail[0]->menuName ?></span>
                                        <span class="pcoded-mcaret"></span>
                                    </a>
                                </li>
                                <?php  
                                }
                                ?>
                                
                            </ul>
                    </div>
                    </nav>
                    <div class="pcoded-content">
                        <div class="pcoded-inner-content">
                            <div class="main-body">
                                <div class="page-wrapper">
                                    <div class="page-body">
                                        <div class="row">         
                                            <div class="col-sm-12" id="iCen">
                                                            
                                            </div>  
                                            </div>
                                        </div>
                                    </div>

                                    <div id="styleSelector">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
               
            </div>
        </div>
<img id="img" name = "img" src='' style="display: none;"/>    
		
		
</body>

</html>
