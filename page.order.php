<?php
include("include/nctsClass.php");
include('include/xml.php');
session_start();

date_default_timezone_set("Asia/Bangkok");
$nctsXML = new SimpleXMLElement($xmlstr);
$nCompany = $nctsXML->ncts[0]->genId->nCompany;;
$channelAccessToken = $nctsXML->ncts[0]->genId->channelAccessToken;
$channelSecret = $nctsXML->ncts[0]->genId->channelSecret;
$liffId = $nctsXML->ncts[0]->genId->liffId;
$page = $nctsXML->ncts[0]->genId->page;
$firstAdmin = $nctsXML->ncts[0]->genId->firstAdmin;
$n = new ncts("","","");
$u = json_decode($_SESSION['iUser']);
$status = $u[0]->status;
$aId=$u[0]->memId;
$date = date("Y-m-d H:i:s");
foreach($_POST as $key=>$val){
    $$key = $val;
}

$sqlGameRoom = "SELECT gameRoomName,gameRoomId FROM gameRoom";
$dataGameRoom = $n->sel($sqlGameRoom);


if (isset($_POST['page'])) {
    $currentPage = $_POST['page'];
    if ($currentPage == "streaming") {?>
        <div class="d-flex justify-content-center mt-5">
          <div class="card text-center">
            <div class="card-header">
              Streaming
            </div>
            <div class="card-body">
              <div class="icen">

              </div>
              <button class="btn btn-primary" id="startStream">start</button>
            </div> 
          </div>
        </div>

    <?php } else if ($currentPage == "gameRoom") {?>
      <div class="container mt-3">
        <div class="row ">

          <?php foreach($dataGameRoom as $data){?>
            <div class="col-3">
              <div class="card text-center w-100">
                <div class="card-header">
                  <?php echo $data->gameRoomName ?>
                </div>
                <div class="card-body">
                  <img src="img/logo.jpg" alt="" width="50px">
                  <div class="icen">
                    
                  </div>
                  <button class="btn btn-primary mt-3" id="joinRoom" onclick="joinRoom('<?php echo $data->gameRoomId ?>')">เลือก</button>
                </div> 
              </div>
            </div>
            <?php } ?>

        </div>
      </div>
   <?php } 
}
?>
<script>
  const whip = <?php echo $whip  ?>
$(document).ready(function(){
  

  $(document).on("click","#startStream",function(){
    var formdata = new FormData();
    formdata.append("whip",whip);
    $.ajax({
      url:"webrtc/src/whip.php",
      type:"POST",
      data:FormData,
      dataType:"html",
      contentType:false,
      processData:false,
      success:function(data){
        $('#iCen').html(data);
      }
    });
  });
});


function joinRoom(roomId){
  var formdata = new FormData();
  formdata.append("gameRoomId",roomId);
  $.ajax({
      url:"td.php",
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