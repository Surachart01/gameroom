<?php
  include("include/nctsClass.php");
  $n = new ncts("","","");
  $sqlProvince = "SELECT * FROM province";
  $qProvince = $n->sel($sqlProvince);
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

    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
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
  <div class="container">
      <div class="card border-dark mt-5 shadow">
        <div class="card-header d-flex justify-content-center">
          <h4 class="card-title my-3">Register</h4>
        </div>

        <div class="card-body">
            <div class="container">
                <input type="text" placeholder="ชื่อ" id="memName" class="form-control mt-2">
              <input type="text" placeholder="นามสกุล" id="memSur" class="form-control mt-2">
              <input type="text" placeholder="ชื่อภายในเว็บ" id="nickName" class="form-control mt-2">
              <input type="email" placeholder="email" id="email" class="form-control mt-2">
              <input type="password" placeholder="password" id="password" class="form-control mt-2">
              <input type="tel" placeholder="เบอร์โทร" id="tel" class="form-control mt-2">
              <input type="text" placeholder="ที่อยู่" id="address" class="form-control mt-2">
              
              <select  id="province" class="form-select mt-2"> 
                <option value="" selected>เลือกจังหวัดของคุณ</option>
                <?php
                  foreach($qProvince as $data){?>
                    <option value="<?php echo $data->PROVINCE_ID ?>"><?php echo $data->PROVINCE_NAME ?></option>
                  <?php }
                ?>
              </select>
              <select  id="amphur" class="form-select mt-2">
                <option value="" selected>เลือกอำเภอของคุณ</option>
              </select>
              <select  id="district" class="form-select mt-2">
                <option value="" selected>เลือกตำบลของคุณ</option>
              </select>

              <button class="btn btn-warning mt-3 form-control" id="submit_register">สม้ครสมาชิก</button>
            </div>

        </div>
          
        </div>
      </div>
  </div>


  <script>
    $(document).ready(function(){

      $('#amphur').change(function(){
          var amphurId = $('#amphur').val();
          var formdata = new FormData();
          formdata.append("AMPHUR_ID",amphurId);
          $.ajax({
            url:"iAmphur.php",
            type:"POST",
            data:formdata,
            dataType:"text",
            contentType:false,
            processData:false,
            success:function(data){
              $('#district').append(data);
            }
          });
      });

      $('#province').change(function(){
          var provinceId = $('#province').val();
          var formdata = new FormData();
          formdata.append("PROVINCE_ID",provinceId);
          $.ajax({
            url:"iProvince.php",
            type:"POST",
            data:formdata,
            dataType:"text",
            contentType:false,
            processData:false,
            success:function(data){
              $('#amphur').append(data);
            }
          });
      });



      $(document).on("click","#submit_register",function(){
          var memName = $("#memName").val();
          var memSur = $('#memSur').val();
          var email = $('#email').val();
          var password = $('#password').val();
          var tel= $('#tel').val();
          var address = $('#address').val();
          var provice = $('#province').val();
          var amphur = $('#amphur').val();
          var district = $('#district').val();
          var nickName = $('#nickName').val();

          var formdata = new FormData();
          formdata.append("memName",memName);
          formdata.append("memSur",memSur);
          formdata.append("email",email);
          formdata.append("password",password);
          formdata.append("tel",tel);
          formdata.append("address",address);
          formdata.append("provice",provice);
          formdata.append("amphur",amphur);
          formdata.append("district",district);
          formdata.append("nickName",nickName);

          $.ajax({
            url:"check.register.php",
            type:"POST",
            data:formdata,
            dataType:"json",
            contentType:false,
            processData:false,
            success:function(data){
              if(data == 1){
              Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: 'เสร็จสิ้น',
                showConfirmButton: false,
                timer: 1500
              }).then((result) => {
                window.location.href = "login.php";
              });
            }else{
              Swal.fire({
                position: 'top-end',
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                showConfirmButton: false,
                timer: 1500
              });
            }
            }
          });
      });
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Bootstrap JavaScript Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
    integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
    integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
  </script>
</body>

</html>