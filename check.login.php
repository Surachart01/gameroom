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

    $sqlUser = "SELECT * FROM member WHERE email='$email' AND password='$pass'";
    $qUser = $n->sel($sqlUser);
    $rUser = count($qUser);
    if($rUser == 1){
        $_SESSION['iUser'] = $qUser;
    }

    echo json_encode($rUser);


   

?>