<?php  
    include("../../include/nctsClass.php");
    include('../../include/xml.php');
    session_start();
    $n = new ncts("","","");
    $user = $_SESSION['iUser'];
    foreach($_POST as $key=>$val){
        $$key = $val;
     }
    $sqlTd ="SELECT * FROM td WHERE whip='$whip'";
    $qTd = $n->sel($sqlTd);
    $uid = $qTd[0]->rtcId;
    $sqlDelTd = "DELETE FROM td WHERE rtcId='$uid'";
    $qDelTd = $n->inUp($sqlDelTd);
    if($qDelTd == 1){
        $apiToken = 'MW6TO-rif8IsuRhyPCQIZnOu6naErkE8J40ZBHmK';
        $accountId = '60bb68256ae4a59061695aa111837ac1';
        $liveId = $qTd[0]->rtcId;
        $curl = curl_init();
        $url = "https://api.cloudflare.com/client/v4/accounts/".$accountId."/stream/live_inputs/".$liveId ;

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $apiToken,
                "content-Type: application/json"
            ]
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
        echo json_encode("cURL Error #:" . $err);
        } else {
        echo json_encode($response);
        }
    }
?>