<?php 
include('nctscClass.php');
?>
<!doctype html>
<html lang="en">

<head>
  <title>Login</title>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <!-- Bootstrap CSS v5.2.1 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">

<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>

<!-- Option 1: Include in HTML -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

<script src="https://kit.fontawesome.com/ce6754fea4.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script charset="utf-8" src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
</head>

<style>
  body{
    background-color: #F5EBDA;
  }
  .card{
    background-color: #FFFFF4;
  }
</style>

<body>
<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/th_TH/sdk.js#xfbml=1&version=v17.0&appId=1302767303666364&autoLogAppEvents=1" nonce="4Jx0oEz7"></script>
<script src="https://accounts.google.com/gsi/client" async defer></script>
    <div class="container w-100 p-4  pb-4 mt-5">  

          <div class="card border-dark rounded-2 shadow">
            <div class="card-header justify-content-center d-flex">
              <h5 class="my-3">Login</h5>
            </div>
            <div class="card-body">
            <div class="form-outline mb-4">
                <input type="email" name="email" id="email" class="form-control" placeholder="Email">
                
            </div>
            <div class="form-outline mb-4">
                <input type="password" name="password" id="password" class="form-control" placeholder="Password">
                
            </div>
            <div class="text-center">
              <p>Not a member? <a href="register.php">Register</a></p>
              <p>or sign up with:</p>
              <div class="d-flex justify-content-center">
                  <div id="g_id_onload"
                      data-client_id="90310662516-tvjgpk3jb2le08ra5jpfokiinlk8t4to.apps.googleusercontent.com"
                      data-context="signin"
                      
                      data-ux_mode="popup"
                      data-callback="getProfile"
                      data-auto_prompt="false">
                  </div>

                  <div class="g_id_signin"
                      data-type="icon"
                      data-shape="circle"
                      data-theme="outline"
                      data-text="signin_with"
                      data-size="large">
                  </div>

                  <button type="button" class="btn btn-white px-1 py-1">
                  <a   href="login.liff.php" ><i class="fa-brands fa-line mx-1 my-1" style="font-size:30px; color:#00b900 ;"></i></a>
                  </button>
              </div>
            </div>
            <button type="button" class="btn btn-success form-control mb-4 mt-2" id="submit">Sing in</button>


          </div>
            </div>
            
           
    </div>
    <script>
          


        $(document).ready(function(){

          $(document).on("click","#liff",function(){
            loginLiff();
          });

            $('#submit').on('click',function(){
                var email = $('#email').val();
                var pass = $('#password').val();
                var formdata = new FormData();
                formdata.append("email",email);
                formdata.append("pass",pass);
                $.ajax({
                    url: 'check.login.php',
                    type: 'POST',
                    data:formdata,
                    dataType:'json',
                    contentType:false,
                    processData:false,
                    success:function(data){
                        if(data==1){
                          Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: 'ล็อคอินสำเร็จ',
                            showConfirmButton: false,
                            timer: 1500
                          }).then((result) => {
                            window.location.href = "index2.php?page=gameRoom";
                          });
                        }
                        if(data>1 || data<1){
                          Swal.fire({
                            position: 'top-end',
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            showConfirmButton: false,
                            timer: 1500
                          });
                        }
                    }
                })
            })
        });

        async function loginLiff(){
          liff.init({ liffId: "2000369709-DxRo3W7Z" }, async () => {
            if (!liff.isLoggedIn()) {
                liff.login({ redirectUri: 'https://www.nctsc.space/gameroom/login.php' });
            } else {
                getProfileLine();
            }
          });
        }

        function getProfileLine() {
          try {
              var profile =  liff.getProfile();
              var userId = profile.userId;
              var pictureUrl = profile.pictureUrl;
              var displayName = profile.displayName;
              var email = liff.getDecodedIDToken().email;

              var formdata = new FormData();
              formdata.append("mId", userId);
              formdata.append("pictureUrl", pictureUrl);
              formdata.append("displayName", displayName);
              formdata.append("email", email);

              $.ajax({
              url:"liff.register.php",
              type:"POST",
              data:formdata,
              dataType:"json",
              contentType:false,
              processData:false,
              success:function(data){
                  if(data == 1){
                      Swal.fire({
                          position:"top-end",
                          icon:"success",
                          title:"login success",
                          showConfirmButton:false,
                          timer:900
                      }).then((result) => {
                          window.location.href = "index2.php";
                      });
                  }else{
                      Swal.fire({
                          position:"top-end",
                          icon:"error",
                          title:"login fail",
                          showConfirmButton:false,
                          timer:900
                      }).then((result) => {
                          window.location.href = "login.php";
                      });
                  }
              }
          });
          } catch (error) {
              console.error("เกิดข้อผิดพลาด: " + error);
          }
        }

        function decodeJwt(token) {
        var base64Payload = token.split(".")[1];
        var payload = decodeURIComponent(
          atob(base64Payload)
            .split("")
            .map(function (c) {
              return "%" + ("00" + c.charCodeAt(0).toString(16)).slice(-2);
            })
            .join("")
        );
        return JSON.parse(payload);
      }


        function getProfile(response){
          var responsePayload = decodeJwt(response.credential);
          var fullname = responsePayload.name;
          var id = responsePayload.sub;
          var email = responsePayload.email;
          var image = responsePayload.picture;
          var formdata = new FormData();
          formdata.append("fullname",fullname);
          formdata.append("gId",id);
          formdata.append("email",email);
          formdata.append("pictureUrl",image);
          
          $.ajax({
            url:"gmail.register.php",
            type:"POST",
            data:formdata,
            dataType:"json",
            contentType:false,
            processData:false,
            success:function(data){
              console.log(data);
              if(data == 1){
              Swal.fire({
                position:"top-end",
                title:"login success",
                icon:"success",
                showConfirmButton:false,
                timer:1000
              }).then((result) => {
                window.location.href = "index2.php";
              });
              }else {
                Swal.fire({
                  position:"top-end",
                  title:"login error",
                  icon:"error",
                  showConfirmButton:false,
                  timer:1000
                });
              }
            }
          });
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