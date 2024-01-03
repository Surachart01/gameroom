<?php
include("include/nctsClass.php");
include('include/xml.php');

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



$sqlCheck ="SELECT memShort FROM member ";
$user = $n->sel($sqlCheck);

$row = count($user);
$rp=intval($row)+1;
$a="G";
$b="00000";
$c=strlen($rp);
$d=substr($b,$c);
$memShort=$a.$d.$rp;

$memId = md5($memShort.$date);


$sql = "INSERT INTO member (memId,memShort,memName,memSur,nickName,email,password,address,PROVINCE_ID,AMPHUR_ID,DISTRICT_ID,tel,memberDate,nCompany) VALUES('$memId','$memShort','$memName','$memSur','$nickName','$email','$password','$address','$provice','$amphur','$district','$tel','$date','$nCompany')";

$in = $n->inUp($sql);

echo json_encode($in)
?>