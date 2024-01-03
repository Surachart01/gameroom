<?php
    include("include/nctsClass.php");
    include('include/xml.php');
    session_start();
    
    date_default_timezone_set("Asia/Bangkok");
    $nctsXML = new SimpleXMLElement($xmlstr);
    $nCompany = $nctsXML->ncts[0]->genId->nCompany;
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
    $sqlTD = "SELECT * FROM td WHERE gameRoomId='$gameRoomId'";
    $qTD = $n->sel($sqlTD);
?>
    <div class="row my-3">
        <div class="col-12">
            <div class="card text-start">
                <div class="card-header d-flex justify-content-end">
                    <button class="btn btn-success" id="liveStream" onclick="liveStream('<?php echo $gameRoomId; ?>')">Streamimg</button>
                </div>
              <div class="card-body">
             <?php 
    foreach($qTD as $data){ ?>
              <div class="card shadow">
                <div class="card-header d-flex jusutify-content-between">
                        <div class="text-success my-auto">* streamimg</div>
                        <div class="h-6 me-auto my-auto"><?php echo "  ".$data->tdDatetime ?></div>
                        <button class="btn btn-warning" id="streamimg" onclick="WatchStream('<?php echo $data->whep; ?>')" >watch streamimg</button>
                </div>
            </div>


              </div>
            </div>
            
        </div>
    </div>
<?php } ?>

<script>

        function WatchStream(whep){
            window.location.href = "webrtc/src/whep.php?data="+whep;
        }

        function liveStream(gameroomId){
            var formdata = new FormData();
            var fd = new FormData();
            formdata.append("gameroomId",gameroomId);
            $.ajax({
                url:"liveStream.php",
                type:"POST",
                data:formdata,
                dataType:"json",
                contentType:false,
                processData:false,
                success:function(data){
                    if(data == 0){
                           Swal.fire({
                                position:"top-end",
                                title:"เกิดข้อผิดพลาด",
                                icon:"error",
                                showConfirmButton:false,
                                timer:1000
                            }); 
                    }else if(data==2){
                            Swal.fire({
                                position:"top-end",
                                title:"ห้อง Streamimg ไม่ว่าง",
                                icon:"error",
                                showConfirmButton:false,
                                timer:1000
                            }); 
                    }else{
                         window.location.href = "webrtc/src/whip.php?data="+data;   
                    }
                }
            });
        }

</script>

