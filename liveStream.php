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
$sqlTd = "SELECT gameRoomId FROM td WHERE gameRoomId='$gameroomId'";
$qTd = $n->sel($sqlTd);
$rTd = count($qTd);
if($rTd == 0){
    $apiToken = 'MW6TO-rif8IsuRhyPCQIZnOu6naErkE8J40ZBHmK';
    $accountId = '60bb68256ae4a59061695aa111837ac1';
    $url = "https://api.cloudflare.com/client/v4/accounts/{$accountId}/stream/live_inputs";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $apiToken,
        'Content-Type: application/json'
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if (isset($response)) {
        $dataWebRTC = json_decode($response);
        $whip = $dataWebRTC->result->webRTC->url;
        $whep = $dataWebRTC->result->webRTCPlayback->url;
        $rtcId = $dataWebRTC->result->uid;
        $sqlInStream = "INSERT INTO td (tdDatetime,gameRoomId,tdStatus,whip,whep,rtcId) VALUES ('$date','$gameroomId','1','$whip','$whep','$rtcId')";
        $qStream = $n->inUp($sqlInStream);
        if($qStream == 1){
            echo json_encode($whip);
        }else{
            echo json_encode("0");
        }
    }
    curl_close($ch);
}else{
    echo json_encode("2");
}



?>
