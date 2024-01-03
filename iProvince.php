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
$n = new ncts($channelAccessToken,$channelSecret,$nCompany);
$n->rePage($_POST["PROVINCE_ID"],"login.php");
foreach($_POST as $key=>$val){
	$$key = $val;
}
$sql = "select * from amphur where PROVINCE_ID='$PROVINCE_ID'";
$q = $n->sel($sql);
$r = count($q);
if($r>0)
{
    for($i=0;$i<$r;$i++)
    {	
        $AMPHUR_ID = $q[$i]->AMPHUR_ID;
        $AMPHUR_NAME=$q[$i]->AMPHUR_NAME;
        echo "<option value='$AMPHUR_ID'>$AMPHUR_NAME</option>";
        
    }
	
}

?>