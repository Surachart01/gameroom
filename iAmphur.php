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
$n->rePage($_POST["AMPHUR_ID"],"login.php");
$iSub = $_SESSION["iSub"];
foreach($_POST as $key=>$val){
	$$key = $val;
}
$sql = "select * from district where AMPHUR_ID='$AMPHUR_ID'";
$q = $n->sel($sql);
$r = count($q);
if($r>0)
{
    for($i=0;$i<$r;$i++)
    {	
        $DISTRICT_ID = $q[$i]->DISTRICT_ID;
        $DISTRICT_NAME=$q[$i]->DISTRICT_NAME;
        echo "<option value='$DISTRICT_ID'>$DISTRICT_NAME</option>";
    }
	
}

?>