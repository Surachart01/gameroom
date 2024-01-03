<?php
#-------------------------[Include]-------------------------#
require_once('include/nctsClass.php');
require_once('include/xml.php');
require_once('include/gameArray.php');
// require_once('unirest-php-master/src/Unirest.php');

#-------------------------[Token]-------------------------#
date_default_timezone_set("Asia/Bangkok");
$today=date('Y-m-d H:i:s');
$nctsXML = new SimpleXMLElement($xmlstr);
$companyId = $nctsXML->ncts[0]->genId->companyId;;
$channelAccessToken = $nctsXML->ncts[0]->genId->channelAccessToken;
$channelSecret = $nctsXML->ncts[0]->genId->channelSecret;
$nCompany = $nctsXML->ncts[0]->genId->nCompany;
$page = $nctsXML->ncts[0]->genId->page;
$firstAdmin = $nctsXML->ncts[0]->genId->firstAdmin;
$n = new ncts($channelAccessToken,$channelSecret,$nCompany);
$greetingMsg = $nCompany.' ยินดีต้อนรับค่ะ';
$fol = $page; // หน้าหลัก
//$card = $page."/images/card/";
$hook = file_get_contents('php://input');
$uf = "baandan@nctsc.com";
$pf = "7In5AtN8cn";
$hf = "ftp.nctsc.com:2002";


#-------------------------[Events]-------------------------#

$fetchData = $n->fetchData();
$userId     = $fetchData['events'][0]['source']['userId'];
$replyToken = $fetchData['events'][0]['replyToken'];
$groupId    = $fetchData['events'][0]['source']['groupId'];
$type       = $fetchData['events'][0]['type'];
$message    = $fetchData['events'][0]['message'];
$profile    = $n->profil($userId);
$messageid  = 	$fetchData['events'][0]['message']['id'];
$msg_type      = $fetchData['events'][0]['message']['type'];

$post_data      = $fetchData['events'][0]['postback']['data'];
$post_param 	= $fetchData['events'][0]['postback']['params']['datetime'];

$msg_message   = $fetchData['events'][0]['message']['text'];
$msg_title     = $fetchData['events'][0]['message']['title'];
$msg_address   = $fetchData['events'][0]['message']['address'];
$msg_latitude  = $fetchData['events'][0]['message']['latitude'];
$msg_longitude = $fetchData['events'][0]['message']['longitude'];

//สร้างการเช็คตรงนี้ว่าเป็นadmin หรือ User
$n->changeMenu($userId);

#----Check title empty----#
if (empty($msg_title)) {
    $msg_title = $greetingMsg;
}
#----command option----#
$trimMsg = trim(strtoupper($msg_message));
$command = $trimMsg;

#----command option----#
$reline = json_encode($profile, true);
$reline1 = json_decode($reline, true);
$displayName = $reline1['displayName'];
$pictureUrl =  $reline1['pictureUrl'];
$statusMessage = $reline1['statusMessage'];
$sqlMember="select status from member where mId='$userId'";
$qMember=$n->sel($sqlMember);
$rMember=count($qMember);
if($rMember==0)
{
	if($groupId!=""){exit();}
	$n->blacklist($userId);
	$response = $n->insertMember($userId,$displayName,$pictureUrl,$nCompany,$today);
	$txt = 'ขอบคุณที่กลับเข้ามาเยี่ยมชมเราอีกครั้งหนึ่งค่ะ';
	$response=$n->replyMsg($replyToken,$txt);
	exit();
}else{
	$status=$qMember[0]->status;
}

/* $pattern = '/\?[A-Z]{2}=/';
if(preg_match($pattern, $trimMsg))
{
	$pt=explode('?',$trimMsg);
	$tm=$pt[1];
    $command=trim(mb_substr($tm,0,2,'UTF-8'));
	$options=$pt[1];
} */

$pattern = '/\?[a-zA-Z0-9]+=/';
if(preg_match($pattern, $trimMsg))
{
	$pt=explode('?',$trimMsg);
    $command=$pt[0];
	$options=$pt[1];
}



if ($type == 'memberJoined') {
	$response = $n->insertMember($userId,$displayName,$pictureUrl,$nCompany,$today);
    exit();

}else if ($type == 'memberLeft') {
    $txt = "MEMBER LEFT THE GROUP";
    $response = $n->replyMsg($replyToken,$txt);
    exit();
}else if ($type == 'join') {
    $txt = "สวัสดีค่ะความสนุกได้เริ่มขึ้นแล้ว";
	$response = $n->replyMsg($replyToken,$txt);
    exit();
}else if ($type == 'leave') {
    $txt = "BOT LEAVE THE GROUP";
	$response = $n->replyMsg($replyToken,$txt);
    exit();
}else if ($type == 'follow') {
	$response = $n->insertMember($userId,$displayName,$pictureUrl,$nCompany,$today);
    $txt = 'ขอบคุณที่กลับเข้ามาเยี่ยมชมเราอีกครั้งหนึ่งค่ะ';
	$response = $n->replyMsg($replyToken,$txt);
    exit();
}else if ($type == 'unfollow') {
    //$n->insertMember();
    exit();
}else if ($msg_type == 'image') {
	$sql="select * from followAdmin where adminId='$userId' and step='2'";
	$q=$n->sel($sql);
	$r=count($q);
	if($r>0)
	{
		$problemId=$q[0]->problem;
		$detail=$q[0]->detail;

		$url = 'https://api-data.line.me/v2/bot/message/'.$messageid.'/content';
		$headers = array('Authorization: Bearer ' . $channelAccessToken);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$result = curl_exec($ch);
		$err = curl_error($ch);
		curl_close($ch);
		$ran = date('YmdHis');
		$f = $userId . "_" . $ran . '.png';
		$hostname = 'ftp://'.$uf.':'.$pf.'@'.$hf.'/domains/nctsc.com/public_html/bandan/images/works/'.$f;

		$picurl = 'https://www.nctsc.com/bandan/images/works/'.$f;
		file_put_contents($hostname,$result);

		$dt=date('Y-m-d H:i:s');
		$pic='images/works/'.$f;
		$sql3="insert into problemFix(problemId,problemNote,datetime,adminId,pic) values('$problemId','$detail','$dt','$userId','$pic')";
		$q3=$n->inUp($sql3);
		$sql4="update problem set problemNote='$detail' where problemId='$problemId'";
		$q4=$n->inUp($sql4);
		$data=finishImg($picurl,$problemId);
		$fData=json_decode($data);
		$response=$n->replyFlexMsg($replyToken,$fData);
		exit();	
	}

}else if($msg_type == 'location') {	
    if($groupId!=""){exit();}
		$n->blacklist($userId);
		$sql="select * from follow where memId='$userId' and sDatetime>='$today' and step='2'";
		$q=$n->sel($sql);
		$r=count($q);
		if($r>0)
		{
			$location=$msg_address.','.$msg_latitude.','.$msg_longitude;
			$followId=$q[0]->followId;
			$problemTypeId=$q[0]->problemTypeId;
			$sqlUp="update follow set location='$location' where followId='$followId'";
			$qUp=$n->inUp($sqlUp);
			if($qUp)
			{
				$sDatetime=date('Y-m-d H:i:s');
				$problemName=$q[0]->problem;
				$lo=$msg_latitude.','.$msg_longitude;
				$sql="insert into problem(memId,sDatetime,problemName,problemTypeId,address,location,problemStatus) values('$userId','$sDatetime','$problemName','$problemTypeId','$msg_address','$lo','0')";
				$q=$n->inUp($sql);
				$sqlUp="delete from follow where memId='$userId' ";
				$qUp=$n->inUp($sqlUp);
				$txt="✅  แอดมินรับเรื่องแล้วค่ะ ตอนนี้กำลังประสานงานกับเจ้าหน้าที่เพื่อดำเนินการให้เร็วที่สุด ขอบคุณค่ะ";
				$response=$n->replyMsg($replyToken,$txt);

				$sql1="select memId from member where status='1' || status='9'";
				$q1=$n->sel($sql1);
				$r1=count($q1);
				$arr=array();
				for($i=0;$i<$r1;$i++)
				{
					$arr[$i]=$q1[$i]->memId;
				}
				$txt='มีผู้แจ้งปัญหาเข้ามาผู้ดูแลระบบกรุณาตรวจสอบระบบหลังบ้านด้วยค่ะ';
				$n->sendMultiCast($txt,$arr);
				exit();
			}else{
				$txt="เกิดข้อผิดพลาดกรุณาส่งใหม่ค่ะ";
				$response=$n->replyMsg($replyToken,$txt);
				exit();
			}
			
		}
	exit();

}else{ 
	if($command == 'RID'){
		$endpoint = 'https://api.line.me/v2/bot/user/all/richmenu';
		$headers = [
			'Authorization: Bearer ' . $channelAccessToken
		];
		$ch = curl_init($endpoint);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		$response = json_decode($response);
		curl_close($ch);
		$response = $n->replyMsg($replyToken,$response->richMenuId);
	}
	if($command== 'MYID'){ 
		$response = $n->replyMsg($replyToken,$userId);
	}else if($command=='INSERTMEMBER'){
		$response = $n->replyMsg($replyToken,$response);
	}else if($command==$roomCommand["USERJOIN"][0]){
		if($groupId!=""){exit();}
		$n->blacklist($userId);
		$response = $n->insertMember($userId,$displayName,$pictureUrl,$nCompany,$today);
		$txt = 'ขอบคุณที่กลับเข้ามาเยี่ยมชมเราอีกครั้งหนึ่งค่ะ';
		$response=$n->replyMsg($replyToken,$txt);
		exit();
	}else if($command==$roomCommand["STARTPROBLEM"][0]){
		if($groupId!=""){exit();}
		$n->blacklist($userId);
		$n->checkUser($userId);
		$sql="select * from problemType";
		$q=$n->sel($sql);
		$data = startProblem($q);
		$fData = json_decode($data);
		$response = $n->replyFlexMsg($replyToken,$fData);
		exit();
	}else if($command==$roomCommand["INSERTPROBLEM"][0]){
		if($groupId!=""){exit();}
		$n->blacklist($userId);
		$n->checkUser($userId);
		$op=explode('=',$options);
		/* $txt=$op[0];
		$response=$n->replyMsg($replyToken,$txt);
		exit(); */
		$problemTypeId=$op[1];
		$sDatetime=date('Y-m-d H:i:s');
		$sqlPbType="select * from problemType where problemTypeId='$problemTypeId'";
		$qPbType=$n->sel($sqlPbType);
		$problemTypeName=$qPbType[0]->problemTypeName;

		/* $sql="select * from problem where memId='$userId' and problemStatus='3'";
		$q=$n->sel($sql);
		$r=count($q);
		if($r>0)
		{
			$txt='ผู้ดูแลระบบรับทราบถึงปัญหาของคุณแล้ว \n ตอนนี้กำลังอยู่ระหว่างดำเนินการ ขอบคุณค่ะ';
			$n->replyMsg($replyToken,$txt);
			exit();
			// ปัญหามีการแจ้งแล้ว ส่งค่าสรุปให้ผู้ใช้
		}

		$sql="select * from problem where memId='$userId' and problemStatus='2'";
		$q=$n->sel($sql);
		$r=count($q);
		if($r>0)
		{
			$txt='ระบบรับเรื่องเรียบร้อยแล้วค่ะ \n ตอนนี้กำลังอยู่ระหว่างส่งเจ้าหน้าเพื่อไปตรวจสอบ ขอบคุณค่ะ';
			$n->replyMsg($replyToken,$txt);
			exit();
			// ปัญหามีการแจ้งแล้ว ส่งค่าสรุปให้ผู้ใช้
		} */

		/* $sql="select * from problem where memId='$userId' and problemStatus='0'";
		$q=$n->sel($sql);
		$r=count($q);
		if($r>0)
		{
			$txt='ระบบรับเรื่องเรียบร้อยแล้วค่ะ \n ตอนนี้กำลังอยู่ระหว่างส่งเจ้าหน้าเพื่อไปตรวจสอบ ขอบคุณค่ะ';
			$n->replyMsg($replyToken,$txt);
			exit();
			// ปัญหามีการแจ้งแล้ว ส่งค่าสรุปให้ผู้ใช้
		}else{ */
			$sql="select * from follow where memId='$userId' and request='$command' and problemTypeId='$problemTypeId' and sDatetime>='$today'";
			$q=$n->sel($sql);
			$r=count($q);
			if($r>0)
			{
				$txt='รับทราบการแจ้งปัญหา'.$problemTypeName.'แล้วค่ะ ✅ กรุณาพิมพ์ \n 1.ปัญหาที่จะแจ้ง \n 2.ชื่อที่อยู่ เบอร์โทรศัพท์ติดต่อกลับ \n เพื่อให้เจ้าหน้าที่เทศบาลช่วยเหลือดูแลได้อย่างมีประสิทธิภาพได้เลยค่ะ';
				$n->replyMsg($replyToken,$txt);
				exit();
			}else{
				$sql="select * from follow where memId='$userId' and sDatetime>='$today'";
				$q=$n->sel($sql);
				$r=count($q);
				if($r>0)
				{
					$followId=$q[0]->followId;
					$pbId=$q[0]->problemTypeId;
					if($pbId==$problemTypeId)
					{
						$txt='รับทราบการแจ้งปัญหา'.$problemTypeName.'แล้วค่ะ \n✅ กรุณาพิมพ์ \n 1.ปัญหาที่จะแจ้ง \n 2.ชื่อที่อยู่ เบอร์โทรศัพท์ติดต่อกลับ \n เพื่อให้เจ้าหน้าที่เทศบาลช่วยเหลือดูแลได้อย่างมีประสิทธิภาพได้เลยค่ะ';
						$n->replyMsg($replyToken,$txt);
						exit();
					}else{
						$sqlUp="update follow set problemTypeId='$problemTypeId' where followId='$followId'";
						$qUp=$n->inUp($sqlUp);
						$txt='เปลี่ยนเรื่องการแจ้งปัญหาเป็น'.$problemTypeName.'เรียบร้อยแล้วค่ะ\n ✅ กรุณาพิมพ์ \n 1.ปัญหาที่จะแจ้ง \n 2.ชื่อที่อยู่ เบอร์โทรศัพท์ติดต่อกลับ \n เพื่อให้เจ้าหน้าที่เทศบาลช่วยเหลือดูแลได้อย่างมีประสิทธิภาพได้เลยค่ะ';
						$n->replyMsg($replyToken,$txt);
						exit();
					}
				}else{
					$sqlDel="delete from follow where memId='$userId'";
					$qDel=$n->inUp($sqlDel);
					$txt='รับทราบการแจ้งปัญหา'.$problemTypeName.'\n ✅ กรุณาพิมพ์ \n 1.ปัญหาที่จะแจ้ง \n 2.ชื่อที่อยู่ เบอร์โทรศัพท์ติดต่อกลับ \n เพื่อให้เจ้าหน้าที่เทศบาลช่วยเหลือดูแลได้อย่างมีประสิทธิภาพได้เลยค่ะ';
					$sDatetime = date("Y-m-d H:i:s",strtotime('+10 minutes',strtotime($today)));
					$sql="insert into follow(memId,request,problemTypeId,sDatetime,step) values('$userId','$command','$problemTypeId','$sDatetime','1')";
					$q=$n->inUp($sql);
					$n->replyMsg($replyToken,$txt);
					exit();
				}
				
				
			}
		//}
		
	}else if($command==$roomCommand["PB1OK"][0]){
		if($groupId!=""){exit();}
		$n->blacklist($userId);
		$n->checkUser($userId);
		$sql="select * from follow where memId='$userId' and sDatetime>='$today'";
		$q=$n->sel($sql);
		$r=count($q);
		if($r>0)
		{
			$followId=$q[0]->followId;
			$problemTypeId=$q[0]->problemTypeId;
			$sqlUp="update follow set step='2' where followId='$followId'";
			$qUp=$n->inUp($sqlUp);
			/* $txt="✅  กรุณาปักหมุดตำแหน่งของปัญหาในไลน์นี้ โดยการกดปุ่ม + และเลือกตำแหน่ง (Location)เพื่อให้เจ้าหน้าที่เข้าไปแก้ปัญหาให้ด้วยค่ะ";
			$response=$n->replyMsg($replyToken,$txt); */
			$data=shareLo();
			$fData = json_decode($data);
			$response = $n->replyFlexMsg($replyToken,$fData);
			exit();
			exit();
		}
		exit();
	}else if($command==$roomCommand["DELPROBLEM"][0]){
		if($groupId!=""){exit();}
		$n->blacklist($userId);
		$n->checkUser($userId);
		$sql="select followId from follow where memId='$userId'";
		$q=$n->sel($sql);
		$r=count($q);
		if($r==0)
		{
			$txt = 'ไม่มีปัญหาที่จะลบค่ะ';
			$response=$n->replyMsg($replyToken,$txt);
			exit();
		}
		$sql="delete from follow where memId='$userId'";
		$q=$n->inUp($sql);
		$sql="select * from problem where memId='$userId' and problemStatus='0'";
		$q=$n->sel($sql);
		$r=count($q);
		if($r>0)
		{
			$sqlUp="update problem set problemStatus='4' and userId='$userId'";
			$qUp=$n->inUp($sqlUp);
		}
		$txt = 'ลบการแจ้งปัญหาทั้งหมดเรียบร้อยแล้วค่ะ';
		$response=$n->replyMsg($replyToken,$txt);
		exit();
	}else if($command==$roomCommand["TRACK"][0]){
		if($groupId!=""){exit();}
		$n->blacklist($userId);
		$n->checkUser($userId);
		$n->blacklist($userId);
		$sql="select member.memName,member.memPic,problem.problemName,problem.address,problem.location,problem.problemTypeId,problemType.problemTypeName,problem.problemStatus,problem.memId from member,problem,problemType where member.memId=problem.memId and problem.problemTypeId=problemType.problemTypeId  and (problem.problemStatus<>'1' || problem.problemStatus<>'4') and problem.memId='$userId'";
		$q=$n->sel($sql);
		$r=count($q);
		if($r==0)
		{
			$txt='คุณยังไม่มีปัญหาที่แจ้งค่ะ';
			$response=$n->replyMsg($replyToken,$txt);
			exit();
			
		}else{
			$problemStatus=$q[0]->problemStatus;
			$problemTypeName=$q[0]->problemTypeName;
			$problemName=$q[0]->problemName;
			$problemTypeId=$q[0]->problemTypeId;
			if($problemTypeId==1)
			{
				$aPic=$page.'images/pic/electric.png';
			}else if($problemTypeId==2){
				$aPic=$page.'images/pic/waterSupply.png';
			}else if($problemTypeId==3){
				$aPic=$page.'images/pic/road.png';
			}
			
			if($problemStatus==0)
			{
				$aHead='เจ้าหน้ารับเรื่อง กำลังรอตรวจสอบค่ะ';
				$aBody='คุณ'.$displayName.' แจ้งปัญหาเรื่อง : '.$problemName;
				$aFooter='เทศบาลตำบลบ้านแดน ยินดีให้บริการประชาชน';
				$data=problemTrack($aPic,$aHead,$aBody,$aFooter);
				$fData=json_decode($data);
				$response=$n->replyFlexMsg($replyToken,$fData);
				exit();	
			}else if($problemStatus==2){
				$aHead='กำหนดเจ้าหน้าที่เข้าดูงาน';
				$aBody='คุณ'.$displayName.' แจ้งปัญหาเรื่อง : '.$problemName.'\n';
				$aBody.='ตอนนี้เจ้าหน้าที่กำลังระบุเจ้าหน้าที่เพื่อเข้าดูงานค่ะ';
				$aFooter='เทศบาลตำบลบ้านแดน ยินดีให้บริการประชาชน';
				$data=problemTrack($aPic,$aHead,$aBody,$aFooter);
				$fData=json_decode($data);
				$response=$n->replyFlexMsg($replyToken,$fData);
				exit();	
			}else if($problemStatus==3){
				$aHead='เจ้าหน้ากำหนดบุคคลากรเข้าดูงาน';
				$aBody='คุณ'.$displayName.' แจ้งปัญหาเรื่อง : '.$problemName.'\n';
				$aBody.='ตอนนี้ระบุเจ้าหน้าที่รับเรื่องดูงานแล้วค่ะ เจ้าหน้าที่อาจติดต่อกลับเพื่อนัดเวลางาน';
				$aFooter='เทศบาลตำบลบ้านแดน ยินดีให้บริการประชาชน';
				$data=problemTrack($aPic,$aHead,$aBody,$aFooter);
				$fData=json_decode($data);
				$response=$n->replyFlexMsg($replyToken,$fData);
				exit();	
			}else if($problemStatus==1){
				$aHead='การแก้ปัญหาเสร็จเรียบร้อยแล้วค่ะ';
				$aBody='คุณ'.$displayName.' แจ้งปัญหาเรื่อง : '.$problemName.'\n';
				$aBody.='การแก้ปัญหาเสร็จเรียบร้อย และคุณได้ประเมินงานไปแล้ว ขอบคุณค่ะ';
				$aFooter='เทศบาลตำบลบ้านแดน ยินดีให้บริการประชาชน';
				$data=problemTrack($aPic,$aHead,$aBody,$aFooter);
				$fData=json_decode($data);
				$response=$n->replyFlexMsg($replyToken,$fData);
				exit();	
			}
			
		}

		
	}else if($command==$roomCommand["TEST"][0]){

		$data=shareLo();
		$fData = json_decode($data);
		$response = $n->replyFlexMsg($replyToken,$fData);
		
	}else if($command==$roomCommand["SHOWWORK"][0]){
		$n->blacklist($userId);
		$n->checkAdmin($userId);
		$sqlCk="select member.memName,problem.problemId,problemAdmin.problemAdminStatus from member,problem,problemAdmin where member.memId=problem.memId and problem.problemId=problemAdmin.problemId and problemAdmin.adminId='$userId' and (problemAdmin.problemAdminStatus<>'4' and problemAdmin.problemAdminStatus<>'5') and (problem.problemStatus='2' || problem.problemStatus='3')";
		$qCk=$n->sel($sqlCk);
		$rCk=count($qCk);
		/* $response=$n->replyMsg($replyToken,$sqlCk);
			exit(); */
		if($rCk==0)
		{
			$txt='คุณไม่มีงานในระบบค่ะ';
			$response=$n->replyMsg($replyToken,$txt);
			exit();
		}

		$data=showAllWork($qCk);
		$fData = json_decode($data);
		$response = $n->replyFlexMsg($replyToken,$fData);
		exit();
		
	}else if($command==$roomCommand["WORKOK"][0]){

		$n->blacklist($userId);
		$n->checkAdmin($userId);
		$op=explode('=',$options);
		$tp=$op[0];
		$problemId=$op[1];
		$sql="select member.memName,member.memPic,problem.problemName,problem.address,problem.location,problemType.problemTypeName,problem.problemStatus,problem.memId from member,problem,problemAdmin,problemType where member.memId=problem.memId and problem.problemTypeId=problemType.problemTypeId and problem.problemId=problemAdmin.problemId and problem.problemId='$problemId' and problemAdmin.adminId='$userId'";
		$q=$n->sel($sql);
		$r=count($q);
		
		if($r>0)
		{
			$memName=$q[0]->memName;
			$memPic=$q[0]->memPic;
			$problemName=$q[0]->problemName;
			$problemStatus=$q[0]->problemStatus;
			$memId=$q[0]->memId;
			if($problemStatus==2)
			{
				$sqlUp="update problem set problemStatus='3' where problemId='$problemId'";
				$qUp=$n->inUp($sqlUp);
				$sqlUp="update problemAdmin set problemAdminStatus='1' where problemId='$problemId'";
				$qUp=$n->inUp($sqlUp);
				$txt='ขอบคุณที่รับงาน\n คุณสามารถดำเนินงานได้เลยค่ะ';
				$response=$n->replyMsg($replyToken,$txt);
				$txt='มีเจ้าหน้ารับเรื่องกับงานและกำลังเดินการเพื่อจัดการปัญหาให้เร็วที่สุดค่ะ';
				$pushMsg=$n->pushMsg($memId,$txt);
				exit();
			}
		}
		
		
		
	}else if($command==$roomCommand["WORKNOTOK"][0]){
		$n->blacklist($userId);
		$n->checkAdmin($userId);
		$op=explode('=',$options);
		$tp=$op[0];
		$problemId=$op[1];
		
		$sql2="select * from problem,problemAdmin where problem.problemId=problemAdmin.problemId and problem.problemId='$problemId' and problemAdmin.adminId='$userId' and problemAdmin.problemAdminStatus='1'";
		$q2=$n->sel($sql2);
		$r2=count($q2);
		if($r2>0)
		{
			$txt='คุณรับงานไปแล้วไม่สามารถปฏิเสธงานนี้ได้แล้วค่ะ';
			$response=$n->replyMsg($replyToken,$txt);
			exit();
		}

		$sqlSel="select * from followAdmin where request='$command' and adminId='$userId'";
		$qSel=$n->sel($sqlSel);
		$rSel=count($qSel);
		if($rSel>0)
		{
			$sql3="update problemAdmin set problemAdminStatus='0' where problemId='$problemId' and adminId='$userId'";
			$q2=$n->inUp($sql2);
			$sql2="update followAdmin set problem='$problemId' where adminId='$userId' and request='$command'";
			$q2=$n->inUp($sql2);
			$sql3="update problemAdmin set problemAdminStatus='2' where problemId='$problemId' and adminId='$userId'";
			$q3=$n->inUp($sql3);
		}else{
			$sql2="insert into followAdmin(adminId,request,problem) values('$userId','$command','$problemId')";
			$q2=$n->inUp($sql2);
		}
		
		$txt='กรุณาพิมพ์เหตุผลที่ปฏิเสธงานนี้ด้วยค่ะ';
		$response=$n->replyMsg($replyToken,$txt);

	}else if($command==$roomCommand["WORKNOTOKCF"][0]){
		
		$n->blacklist($userId);
		$n->checkAdmin($userId);
		$sqlFollow="select * from followAdmin where adminId='$userId' ORDER BY followAdminId ASC";
		$qFollow=$n->sel($sqlFollow);
		$rFollow=count($qFollow);

		if($rFollow>0)
		{
				$request=$qFollow[0]->request;
				$problemId=$qFollow[0]->problem;
				$sql2="select * from problem,problemAdmin where problem.problemId=problemAdmin.problemId and problem.problemId='$problemId' and problemAdmin.adminId='$userId' and problemAdmin.problemAdminStatus='2'";
				$q2=$n->sel($sql2);
				$r2=count($q2);
			if($r2>0)
			{
				$op=explode('&',$options);
				$op1=explode('=',$op[1]);
				$bd=$op1[1];
				$sql="update problemAdmin set problemAdminStatus='4',problemAdminNote='$bd' where adminId='$userId' and problemId='$problemId'";
				$q=$n->inUp($sql);
				$sql2="insert into problemAdminLog(problemId,adminId,problemAdminStatus,problemAdminNote) values('$problemId','$userId','4','$bd')";
				$q2=$n->inUp($sql2);
				$sqlDel="delete from followAdmin where problem='$problemId' and adminId='$userId'";
				$qDel=$n->inUp($sqlDel);
				$sql2="select * from problemAdmin where problemId='$problemId'";
				$q2=$n->sel($sql2);
				$r2=count($q2);
				$ck=0;
				for($i=0;$i<$r2;$i++)
				{
					$problemAdminStatus=$q2[$i]->problemAdminStatus;
					if($problemAdminStatus==4)
					{
						$ck++;
					}
				}
				if($ck==$r2)
				{
					$sql="update problem set problemStatus='0' where problemId='$problemId'";
					$q=$n->inUp($sql);
					$sql1="select memId from member where status='1' || status='9'";
					$q1=$n->sel($sql1);
					$r1=count($q1);
					$arr=array();
					for($i=0;$i<$r1;$i++)
					{
						$arr[$i]=$q1[$i]->memId;
					}
					$txt='มีสต๊าฟปฏิเสธงานทั้งหมดกรุณาตรวจสอบข้อมูลให้ถูกต้องใหม่ค่ะ';
					$n->sendMultiCast($txt,$arr);
				}
				$txt='ปฏิเสธงานเรียบร้อยแล้วค่ะ ผู้ดูแลระบบจะติดต่อคุณต่อไป';
				$response=$n->replyMsg($replyToken,$txt);

			}else{
				$txt='คุณปฏิเสธงานนี้ไปแล้วค่ะ';
				$response=$n->replyMsg($replyToken,$txt);
			}
		}


		

		
	}else if($command==$roomCommand["WORKDETAIL"][0]){
		
		$n->blacklist($userId);
		$n->checkAdmin($userId);
		$op=explode('=',$options);
		$tp=$op[0];
		$problemId=$op[1];
		$sql="select member.memName,member.memPic,problem.problemName,problem.address,problem.location,problemType.problemTypeName from member,problem,problemAdmin,problemType where member.memId=problem.memId and problem.problemTypeId=problemType.problemTypeId and problem.problemId=problemAdmin.problemId and problem.problemId='$problemId' and problemAdmin.adminId='$userId'";
		$q=$n->sel($sql);
		$r=count($q);
		if($r>0)
		{
			$memName=$q[0]->memName;
			$memPic=$q[0]->memPic;
			$problemName=$q[0]->problemName;
			$z=explode("\n",$problemName);
			$zr=count($z);
			for($j=0;$j<$zr;$j++)
			{
				$problem.=$z[$j].'\n';
			}
			$address=$q[0]->address;
			$location=$q[0]->location;
			$lo=explode(',',$location);
			$lat=$lo[0];
			$lng=$lo[1];
			$problemTypeName=$q[0]->problemTypeName;
			$h1='งาน'.$problemTypeName;
			$h2='ผู้ติดต่อ : คุณ'.$memName;
			$h3=' ';
			$bd=$problem;
			$ft='ที่อยู่ : '.$address;

			$data=showFlexMessage($h1,$h2,$h3,$bd,$ft);
			$fData=json_decode($data);

			$data2=showLocation($h1,$lat,$lng);
			$fData2=json_decode($data2);

			$response=$n->replyFlexMsg2($replyToken,$fData,$fData2);
			exit();

		}else{
			$txt='คุณไม่ได้อยู่ในระบบงานที่เลือกนี้แล้วค่ะ';
			$response=$n->replyMsg($replyToken,$txt);
			exit();
		}
		
	}else if($command==$roomCommand["SENDWORK"][0]){
		
		$n->blacklist($userId);
		$n->checkAdmin($userId);
		$op=explode('=',$options);
		$tp=$op[0];
		$problemId=$op[1];
		$sql="select member.memName,member.memPic,problem.problemName,problem.address,problem.location,problemType.problemTypeName from member,problem,problemAdmin,problemType where member.memId=problem.memId and problem.problemTypeId=problemType.problemTypeId and problem.problemId=problemAdmin.problemId and problem.problemId='$problemId' and problemAdmin.adminId='$userId' and problemStatus='3'";
		$q=$n->sel($sql);
		$r=count($q);
		if($r>0)
		{
			
			$request=$command;
			$sql0="select * from followAdmin where adminId='$userId' and problem='$problemId'";
			$q0=$n->sel($sql0);
			$r0=count($q0);
			
			if($r0==0)
			{
				$sql="insert into followAdmin(adminId,request,problem) values('$userId','$request','$problemId')";
				$q=$n->inUp($sql);
				$txt='กรุณาพิมพ์ข้อมูลของงานนี้ค่ะ';
				$response=$n->replyMsg($replyToken,$txt);
				exit();
			}else{
				$step=$q0[0]->step;
				$detail=$q0[0]->detail;
				if($step==1)
				{
					$txt='กรุณาพิมพ์ข้อมูลของงานนี้ค่ะ';
					$response=$n->replyMsg($replyToken,$txt);
					exit();
				}else if($step==2){
					$img='https://nctsc.com/bandan/images/icon/finish.png';
					$h1='ข้อมูลการส่งงาน';
					$bd=$detail;
					$bt1='ส่งรูปภาพ';
					$txt1='ส่งภาพ?PI='.$problemId.'&pd='.$detail.'&st=2';
					$bt2='ล้างข้อมูล';
					$txt2='ล้างข้อมูล?problemId='.$problemId;
					$data=confirm($img,$h1,$bd,$bt1,$txt1,$bt2,$txt2);
					$fData=json_decode($data);
					$response=$n->replyFlexMsg($replyToken,$fData);
					exit();
				}
			}	

		}else{
			$txt='งานนี้จบไปแล้ว หรือคุณไม่มีสิทธิในงานนี้ค่ะ';
			$response=$n->replyMsg($replyToken,$txt);
			exit();
		}
		
	}else if($command==$roomCommand["SENDPIC"][0]){
		
		$n->blacklist($userId);
		$n->checkAdmin($userId);
		$op=explode('&',$options);

		$a1=$op[0];
		$op1=explode('=',$a1);
		$problemId=$op1[1];

		$a2=$op[1];
		$op2=explode('=',$a2);
		$detail=$op2[1];

		$a3=$op[2];
		$op3=explode('=',$a3);
		$step=$op3[1];

		

		$sql="select member.memName,member.memPic,problem.problemName,problem.address,problem.location,problemType.problemTypeName from member,problem,problemAdmin,problemType where member.memId=problem.memId and problem.problemTypeId=problemType.problemTypeId and problem.problemId=problemAdmin.problemId and problem.problemId='$problemId' and problemAdmin.adminId='$userId' and problemStatus='3'";
		$q=$n->sel($sql);
		$r=count($q);
		if($r>0)
		{
			
			$request=$command;
			$sql0="select * from followAdmin where adminId='$userId' and request='$request' and step='2'";
			$q0=$n->sel($sql0);
			$r0=count($q0);
			if($r0==0)
			{
				$sql="update followAdmin set request='$request',step='2' where adminId='$userId' and problem='$problemId' ";
				$q=$n->inUp($sql);
				$txt='กรุณาถ่ายภาพแล้วส่งเข้ามาได้เลยค่ะ';
				$response=$n->replyMsg($replyToken,$txt);
				exit();
			}else{
				$txt='กรุณาถ่ายภาพแล้วส่งเข้ามาได้เลยค่ะ';
				$response=$n->replyMsg($replyToken,$txt);
				exit();
			}	

		}else{
			$txt='งานนี้จบไปแล้ว หรือคุณไม่มีสิทธิในงานนี้ค่ะ';
			$response=$n->replyMsg($replyToken,$txt);
			exit();
		}
		
	}else if($command==$roomCommand["DELWORK"][0]){
		
		$n->blacklist($userId);
		$n->checkAdmin($userId);
		$op=explode('=',$options);
		$tp=$op[0];
		$problemId=$op[1];
		$sql="select member.memName,member.memPic,problem.problemName,problem.address,problem.location,problemType.problemTypeName from member,problem,problemAdmin,problemType where member.memId=problem.memId and problem.problemTypeId=problemType.problemTypeId and problem.problemId=problemAdmin.problemId and problem.problemId='$problemId' and problemAdmin.adminId='$userId' and problemStatus='3'";
		$q=$n->sel($sql);
		$r=count($q);
		if($r>0)
		{
			$sqlCk="select * from followAdmin where adminId='$userId' and problem='$problemId'";
			$qCk=$n->sel($sqlCk);
			$rCk=count($qCk);
			if($rCk>0)
			{
				$sql="delete from followAdmin where adminId='$userId' and problem='$problemId'";
				$q=$n->inUp($sql);

				$sql2="select * from problemFix where adminId='$userId' and problemId='$problemId'";
				$q2=$n->sel($sql2);
				$r2=count($q2);

					for($i=0;$i<$r2;$i++)
					{
						$pic=$q2[$i]->pic;
						chmod($pic, 0777);
						unlink($pic);
					}


				$sql3="delete from problemFix where adminId='$userId' and problemId='$problemId'";
				$q3=$n->inUp($sql3);



				$txt='ลบการส่งเรียบร้อยแล้วกรุณากดปุ่มงานในระบบเพื่อส่งข้อมูลใหม่ค่ะ';
				$response=$n->replyMsg($replyToken,$txt);
				exit();
			}else{
				$txt='ไม่มีข้อมูลนี้ในระบบค่ะ';
				$response=$n->replyMsg($replyToken,$txt);
				exit();
			}
			
		}else{
			$txt='งานนี้จบไปแล้ว หรือคุณไม่มีสิทธิในงานนี้ค่ะ';
			$response=$n->replyMsg($replyToken,$txt);
			exit();
		}
		
	}else if($command==$roomCommand["FINISHWORK"][0]){
		
		$n->blacklist($userId);
		$n->checkAdmin($userId);
		$op=explode('=',$options);
		$tp=$op[0];
		$problemId=$op[1];
		$sql="select member.memId,member.memShort,member.memName,member.memPic,problem.problemName,problem.address,problem.location,problemType.problemTypeName from member,problem,problemAdmin,problemType where member.memId=problem.memId and problem.problemTypeId=problemType.problemTypeId and problem.problemId=problemAdmin.problemId and problem.problemId='$problemId' and problemAdmin.adminId='$userId' and problemStatus='3'";
		$q=$n->sel($sql);
		$r=count($q);
		if($r>0)
		{
			$sqlMem="select * from member where memId='$userId'";
			$qMem=$n->sel($sqlMem);
			$memId=$q[0]->memId;
			$memShort=$qMem[0]->memShort;
			$sqlCk="select * from problemFix where adminId='$userId' and problemId='$problemId'";
			$qCk=$n->sel($sqlCk);
			$rCk=count($qCk);
			if($rCk==0)
			{
				$txt='กรุณาส่งงานให้เสร็จเรียบร้อยก่อนค่ะ';
				$response=$n->replyMsg($replyToken,$txt);
				exit();
			}

			$sql="delete from followAdmin where adminId='$userId' and problem='$problemId'";
			$q=$n->inUp($sql);

			$sql="update problemAdmin set problemAdminStatus='5' where adminId='$userId' and problemId='$problemId'";
			$q=$n->inUp($sql);
			
			$sqlF="update problem set eDatetime='$today' and problem='$problemId'";
			$qF=$n->inUp($sqlF);

			$sql1="select * from problemAdmin where problemId='$problemId'";
			$q1=$n->sel($sql1);
			$r1=count($q1);

			$sql2="select * from problemAdmin where problemId='$problemId' and problemAdminStatus='5'";
			$q2=$n->sel($sql2);
			$r2=count($q2);
			if($r1==$r2)
			{
				$sql3="update problem set problemStatus='1',edatetime='$today' where problemId='$problemId'";
				$q3=$n->inUp($sql3);
			}

			$txt='ส่งานเรียบร้อยแล้วค่ะ \nระบบกำลังส่งแบบประเมินให้กับผู้แจ้งปัญหา';
			$response=$n->replyMsg($replyToken,$txt);
			$pic=$page.$qCk[0]->pic;
			$problemNote=$qCk[0]->problemNote;
			$z=explode("\n",$problemNote);
			$zr=count($z);
			for($j=0;$j<$zr;$j++)
			{
				$pn.=$z[$j].'\n';
			}
			
			$data=myPoint($problemId,$displayName,$pic,$pn,$memShort);
			$fData=json_decode($data);
			$response=$n->pushFlexMsg($memId,$fData);
			exit();

		}else{
			$txt='งานนี้จบไปแล้ว หรือคุณไม่มีสิทธิในงานนี้ค่ะ';
			$response=$n->replyMsg($replyToken,$txt);
			exit();
		}
		
	}else if($command==$roomCommand["OLDWORK"][0]){
		
		$n->blacklist($userId);
		if($status==1 || $status==2 || $status==9)
		{
			$sql="select member.memName,member.memPic,problem.problemName,problem.address,problem.location,problemType.problemTypeName,problem.problemStatus,problem.memId,problem.problemTypeId,problem.problemNote from member,problem,problemAdmin,problemType where member.memId=problem.memId and problem.problemTypeId=problemType.problemTypeId and problem.problemId=problemAdmin.problemId and problem.problemStatus='1' and problemAdmin.adminId='$userId' ORDER BY problem.problemId DESC LIMIT 0,10";
			$q=$n->sel($sql);
			$r=count($q);
			if($r==0)
			{

				$txt='คุณยังไม่มีงานเก่าที่เคยทำค่ะ';
				$response=$n->replyMsg($replyToken,$txt);
				exit();
				
			}else{
				$data=oldWork($q,$page);
				$fData=json_decode($data);
				$response=$n->replyFlexMsg($replyToken,$fData);
				exit();
			}

		}


		$sql="select member.memName,member.memPic,problem.problemName,problem.address,problem.location,problemType.problemTypeName,problem.problemStatus,problem.memId from member,problem,problemAdmin,problemType where member.memId=problem.memId and problem.problemTypeId=problemType.problemTypeId and problem.problemId=problemAdmin.problemId and problem.problemStatus='1' and problemAdmin.adminId='$userId'";
			$q=$n->sel($sql);
			$r=count($q);
			if($r==0)
			{

				$txt='คุณยังไม่มีงานเก่าที่เคยแจ้งค่ะ';
				$response=$n->replyMsg($replyToken,$txt);
				exit();
				
			}else{
				$txt='ok';
				$response=$n->replyMsg($replyToken,$txt);
				exit();
			}
		
	}else if($command==$roomCommand["ABOUTUS1"][0]){
		$n->changeMenu($userId);
		$sqlUpdateMember = "update member set memName='$displayName',memPic='$pictureUrl' where memId='$userId'";
		$qUpdateMember  = $n->inUp($sqlUpdateMember);
		$op=explode('=',$options);
		$tp=$op[0];
		$problemId=$op[1];
		$sql="select * from aboutUs,aboutUsDetail where aboutUs.aboutUsId=aboutUsDetail.aboutUsId and aboutUs.aboutUsId='1' order by inx";
		$q=$n->sel($sql);
		$r=count($q);
		if($r==0)
		{
			$txt='ยังไม่มีข้อมูลของ '.$command.'ค่ะ';
			$response=$n->replyMsg($replyToken,$txt);
			exit();
		}else{

			$data=aboutUs($q,$page);
			$fData=json_decode($data);
			$response=$n->replyFlexMsg($replyToken,$fData);
			exit();
		}
		
		
	}else if($command==$roomCommand["ABOUTUS2"][0]){
		$n->changeMenu($userId);
		$sqlUpdateMember = "update member set memName='$displayName',memPic='$pictureUrl' where memId='$userId'";
		$qUpdateMember  = $n->inUp($sqlUpdateMember);
		$op=explode('=',$options);
		$tp=$op[0];
		$problemId=$op[1];
		$sql="select * from aboutUs,aboutUsDetail where aboutUs.aboutUsId=aboutUsDetail.aboutUsId and aboutUs.aboutUsId='2' order by inx";
		$q=$n->sel($sql);
		$r=count($q);
		if($r==0)
		{
			$txt='ยังไม่มีข้อมูลของ '.$command.'ค่ะ';
			$response=$n->replyMsg($replyToken,$txt);
			exit();
		}else{
			$data=aboutUs($q,$page);
			$fData=json_decode($data);
			$response=$n->replyFlexMsg($replyToken,$fData);
			exit();
		}
		
		
	}else if($command==$roomCommand["ABOUTUS3"][0]){
		
		$op=explode('=',$options);
		$tp=$op[0];
		$problemId=$op[1];
		$sql="select * from aboutUs,aboutUsDetail where aboutUs.aboutUsId=aboutUsDetail.aboutUsId and aboutUs.aboutUsId='3' order by inx";
		$q=$n->sel($sql);
		$r=count($q);
		if($r==0)
		{
			$txt='ยังไม่มีข้อมูลของ '.$command.'ค่ะ';
			$response=$n->replyMsg($replyToken,$txt);
			exit();
		}else{
			$data=aboutUs($q,$page);
			$fData=json_decode($data);
			$response=$n->replyFlexMsg($replyToken,$fData);
			exit();
		}
		
		
	}else if($command==$roomCommand["ABOUTUS4"][0]){
		$n->changeMenu($userId);
		$sqlUpdateMember = "update member set memName='$displayName',memPic='$pictureUrl' where memId='$userId'";
		$qUpdateMember  = $n->inUp($sqlUpdateMember);
		$op=explode('=',$options);
		$tp=$op[0];
		$problemId=$op[1];
		$sql="select * from aboutUs,aboutUsDetail where aboutUs.aboutUsId=aboutUsDetail.aboutUsId and aboutUs.aboutUsId='4' order by inx";
		$q=$n->sel($sql);
		$r=count($q);
		if($r==0)
		{
			$txt='ยังไม่มีข้อมูลของ '.$command.'ค่ะ';
			$response=$n->replyMsg($replyToken,$txt);
			exit();
		}else{
			$data=aboutUs($q,$page);
			$fData=json_decode($data);
			$response=$n->replyFlexMsg($replyToken,$fData);
			exit();
		}
		
		
	}else if($command==$roomCommand["PMWORK"][0]){
		$n->changeMenu($userId);
		$sqlUpdateMember = "update member set memName='$displayName',memPic='$pictureUrl' where memId='$userId'";
		$qUpdateMember  = $n->inUp($sqlUpdateMember);
		$op=explode('&',$options);
		$PT=$op[0];
		$PID=$op[1];
		$MID=$op[2];
		$po=explode('=',$PT);
		$point=$po[1];
		$p=explode('=',$PID);
		$problemId=$p[1];
		$m=explode('=',$MID);
		$memShort=$m[1];
		$sql1="select * from member where memShort='$memShort'";
		$q1=$n->sel($sql1);
		$aId=$q1[0]->memId;

		/* $txt=$aId.','.$problemId.','.$point;
		$response=$n->replyMsg($replyToken,$txt);
			exit(); */
		$sql2="select * from problemAdmin where adminId='$aId' and problemId='$problemId' and pointReady='0'";
		$q2=$n->sel($sql2);
		$r2=count($q2);
		
		if($r2==0)
		{
			$txt='คุณเคยประเมินไปแล้วค่ะ';
			$response=$n->replyMsg($replyToken,$txt);
			exit();
		}else{
			$problemAdminId=$q2[0]->problemAdminId;
			$sql2="update problemAdmin set point='$point',pointReady='1' where problemAdminId='$problemAdminId'";
			$q2=$n->inUp($sql2);
			$txt='ประเมินงานเรียบร้อย ขอบคุณที่ให้ความร่วมมือค่ะ';
			$response=$n->replyMsg($replyToken,$txt);
			exit();
		}
		

				
	}else if($command==$roomCommand["MOREDATA"][0]){
		$n->blacklist($userId);
		$op=explode('=',$options);
		$aboutUsDetailId=$op[1];
		
		$sql="select * from aboutUsDetail where aboutUsDetailId='$aboutUsDetailId'";
		$q=$n->sel($sql);
		$r=count($q);
		if($r>0)
		{
			$aHead=$q[0]->aHead;
			$aBody=$q[0]->aBody;
			$txt=$aHead;
			$txt.='\n'.$aBody;
			$response=$n->replyMsg($replyToken,$txt);
			exit();
		}
		

				
	}else{

		$n->changeMenu($userId);
		$sqlUpdateMember = "update member set memName='$displayName',memPic='$pictureUrl' where memId='$userId'";
		$qUpdateMember  = $n->inUp($sqlUpdateMember);
		$adminCommand=trim(mb_substr($command,0,2,'UTF-8'));
		$adminOption=trim(mb_substr($command,2));
		$n->blacklist($userId);
		if($status==0)
		{
			$sql="select * from follow where memId='$userId' and sDatetime>='$today'";
			$q=$n->sel($sql);
			$r=count($q);
			if($r>0)
			{
				$request=$q[0]->request;
				$problemTypeId=$q[0]->problemTypeId;
				$followId=$q[0]->followId;
				//$problem=$q[0]->problem;
				$step=$q[0]->step;

				$sqlPbType="select * from problemType where problemTypeId='$problemTypeId'";
				$qPbType=$n->sel($sqlPbType);
				$problemTypeName=$qPbType[0]->problemTypeName;
				if($request==$roomCommand["INSERTPROBLEM"][0])
				{
					if($step==1)
					{
						$sDatetime=date('Y-m-d H:i:s');
						$sql1="update follow set problem='$trimMsg' where followId='$followId'";
						$q1=$n->inUp($sql1);
						if($q1)
						{
							$problem='';
							$z=explode("\n",$trimMsg);
							$zr=count($z);
							for($j=0;$j<$zr;$j++)
							{
								$problem.=$z[$j].'\n';
							}
							$head=$problemTypeName;

							$data=followStep1($head,$displayName,$problem);
							$fData = json_decode($data);
							$response = $n->replyFlexMsg($replyToken,$fData);
							exit();
						}else{
							$txt='เกิดข้อผิดพลาด กรูณาเลือกรายการอีกครั้งหนึ่งค่ะ';
							$request=$n->replyMsg($replyToken,$txt);
							exit();
						}
					}else if($step==2){
						/* $txt="✅  กรุณาปักหมุดตำแหน่งของปัญหาในไลน์นี้ โดยการกดปุ่ม + และเลือกตำแหน่ง (Location) เพื่อให้เจ้าหน้าที่เข้าไปแก้ปัญหาให้ด้วยค่ะ";
						$response=$n->replyMsg($replyToken,$txt); */
						$data=shareLo();
						$fData = json_decode($data);
						$response = $n->replyFlexMsg($replyToken,$fData);
						exit();
					}
				}
			}else{
				$sql="delete from follow where memId='$userId'";
				$q=$n->sel($sql);
				return 0;
			}
		}else if($status==2 || $status==9 || $status==1){
			
			$sqlFollow="select * from followAdmin where adminId='$userId' ORDER BY followAdminId ASC";
			$qFollow=$n->sel($sqlFollow);
			$rFollow=count($qFollow);
			
			if($rFollow>0)
			{
				$request=$qFollow[0]->request;
				$problemId=$qFollow[0]->problem;
				$step=$qFollow[0]->step;

					if($request==$roomCommand["WORKNOTOK"][0])
					{
						$sql2="select * from problem,problemAdmin where problem.problemId=problemAdmin.problemId and problem.problemId='$problemId' and problemAdmin.adminId='$userId' and problemAdmin.problemAdminStatus='2'";
						$q2=$n->sel($sql2);
						$r2=count($q2);
						if($r2>0)
						{
							if($command=='กรุณาพิมพ์เหตุผลที่ปฏิเสธ ได้เลยค่ะ'){exit();}
							$img='https://nctsc.com/bandan/images/icon/question.png';
							$h1='นี่คือเหตุผลที่คุณปฏิเสธงานใช่หรือไม่คะ';
							$bd=$command;
							$bt1='ยืนยันการปฏิเสธ';
							$txt1='ยืนยันการปฏิเสธงาน?PF='.$problemId.'&pd='.$bd;
							$bt2='พิมพ์เหตุผลใหม่';
							$txt2='กรุณาพิมพ์เหตุผลที่ปฏิเสธ ได้เลยค่ะ';
							$data=confirm($img,$h1,$bd,$bt1,$txt1,$bt2,$txt2);
							$fData=json_decode($data);
							$response=$n->replyFlexMsg($replyToken,$fData);
							exit();
						}
					}else if($request==$roomCommand["SENDWORK"][0]){
						if($step==1){
							if($command=='พิมพ์ข้อมูลการส่งงาน ได้เลยค่ะ'){ exit();}
							$sql="update followAdmin set detail='$command' where adminId='$userId' and problem='$problemId'";
							$q=$n->inUp($sql);
							$img='https://nctsc.com/bandan/images/icon/finish.png';
							$h1='ข้อมูลการส่งงาน';
							$z=explode("\n",$command);
							$zr=count($z);
							for($j=0;$j<$zr;$j++)
							{
								$cm.=$z[$j].'\n';
							}
							$bd=$cm;
							$bt1='ส่งรูปภาพ';
							$txt1='ส่งภาพ?PI='.$problemId.'&pd='.$bd.'&st=2';
							$bt2='ล้างข้อมูล';
							$txt2='ล้างข้อมูล?problemId='.$problemId;
							$data=confirm($img,$h1,$bd,$bt1,$txt1,$bt2,$txt2);
							$fData=json_decode($data);
							$response=$n->replyFlexMsg($replyToken,$fData);
							exit();
						}
					}else if($request==$roomCommand["SENDPIC"][0]){
						if($step==2){
							$txt='กรุณาส่งรูปภาพให้เสร็จสมบูรณ์ก่อนค่ะ';
							$response=$n->replyMsg($replyToken,$txt);
							exit();
						}
					}
			}

			
		}

	} // end message
}


function showFlexMessage($h1,$h2,$h3,$bd,$ft){
	$data = '{
		"type": "flex",
		"altText": "Flex Message",
		"contents": {
		  "type": "bubble",
		  "body": {
			"type": "box",
			"layout": "vertical",
			"contents": [
			  {
				"type": "text",
				"text": "'.$h1.'",
				"weight": "bold",
				"color": "#1DB446",
				"size": "sm",
				"wrap": true
			  },
			  {
				"type": "text",
				"text": "'.$h2.'",
				"weight": "bold",
				"size": "xl",
				"margin": "md",
				"wrap": true
			  },
			  {
				"type": "text",
				"text": "'.$h3.'",
				"size": "xs",
				"color": "#aaaaaa",
				"wrap": true
			  },
			  {
				"type": "separator",
				"margin": "xxl"
			  },
			  {
				"type": "box",
				"layout": "vertical",
				"margin": "xxl",
				"spacing": "sm",
				"contents": [
				  {
					"type": "box",
					"layout": "vertical",
					"contents": [
					  {
						"type": "text",
						"text": "'.$bd.'",
						"size": "sm",
						"color": "#555555",
						"wrap": true
					  }
					]
				  }
				]
			  },
			  {
				"type": "separator",
				"margin": "xxl"
			  },
			  {
				"type": "box",
				"layout": "vertical",
				"margin": "md",
				"contents": [
				  {
					"type": "text",
					"text": "'.$ft.'",
					"size": "xs",
					"color": "#aaaaaa",
					"wrap": true
				  }
				]
			  }
			]
		  },
		  "styles": {
			"footer": {
			  "separator": true
			}
		  }
		}
	  }';
	return $data;
}
function showFlexMessgeImg($picUrl,$h1,$data1,$data2,$sta){
	$color = '#00C3AF';
	if($sta=='2')
	{
		$color = '#FF3747';
	}

	$data='{
		"type": "flex",
		"altText": "Flex Message",
		"contents": {
		  "type": "bubble",
		  "hero": {
			"type": "image",
			"url": "'.$picUrl.'",
			"size": "full",
			"aspectRatio": "20:13",
			"aspectMode": "cover"
		  },
		  "body": {
			"type": "box",
			"layout": "vertical",
			"contents": [
			  {
				"type": "text",
				"text": "'.$h1.'",
				"weight": "bold",
				"size": "lg",
				"wrap": true
			  },
			  {
				"type": "box",
				"layout": "vertical",
				"margin": "lg",
				"spacing": "sm",
				"contents": [
				  {
					"type": "box",
					"layout": "vertical",
					"spacing": "sm",
					"contents": [
					  {
						"type": "text",
						"text": "'.$data1.'",
						"color": "'.$color.'",
						"size": "lg",
						"wrap": true
					  },
					  {
						"type": "text",
						"text": "'.$data2.'",
						"wrap": true,
						"color": "#666666",
						"size": "sm"
					  }
					]
				  }
				]
			  }
			]
		  }
		}
	  }';
	return $data;
}

function startProblem($q){
	$r=count($q);

	$data='{
		"type": "flex",
		"altText": "Flex Message",
		"contents": {
		  "type": "bubble",
		  "size": "giga",
		  "header": {
			"type": "box",
			"layout": "vertical",
			"contents": [
			  {
				"type": "box",
				"layout": "horizontal",
				"contents": [
				  {
					"type": "image",
					"url": "https://www.nctsc.com/bandan/images/icon/operator.png",
					"align": "center",
					"aspectRatio": "20:13",
					"size": "full",
					"aspectMode": "cover",
					"gravity": "top"
				  }
				]
			  },
			  {
				"type": "box",
				"layout": "horizontal",
				"contents": [
				  {
					"type": "text",
					"text": "ระบบแจ้งปัญหา เทศบาลตำบลบ้านแดน",
					"color": "#FFFFFF"
				  }
				],
				"position": "absolute",
				"width": "270px",
				"height": "28px",
				"backgroundColor": "#0065A2",
				"cornerRadius": "100px",
				"offsetTop": "8px",
				"offsetStart": "60px",
				"paddingTop": "2px",
				"paddingStart": "4px",
				"paddingEnd": "4px"
			  }
			],
			"position": "relative",
			"paddingAll": "0px"
		  },
		  "body": {
			"type": "box",
			"layout": "vertical",
			"contents": [
			  {
				"type": "text",
				"text": "คุณต้องการแจ้งปัญหาด้านไหนคะ",
				"weight": "bold",
				"size": "xl",
				"align": "center"
			  },
			  {
				"type": "box",
				"layout": "vertical",
				"margin": "lg",
				"spacing": "sm",
				"contents": [
				  {
					"type": "text",
					"text": "โปรดเลือกปัญหาที่ต้องการแจ้งจากเมนูได้เลยค่ะ",
					"color": "#00B0BA",
					"align": "center"
				  }
				]
			  }
			]
		  },
		  "footer": {
			"type": "box",
			"layout": "vertical",
			"spacing": "sm",
			"contents": [';
			for($i=0;$i<$r;$i++)
			{
				if($i>0){$data.=',';}
				$problemTypeId=$q[$i]->problemTypeId;
				$problemTypeName=$q[$i]->problemTypeName;
				$txt='เลือกปัญหา?pb='.$problemTypeId;
		$data.='{
				"type": "button",
				"style": "link",
				"height": "sm",
				"action": {
				  "type": "message",
				  "label": "'.$problemTypeName.'",
				  "text": "'.$txt.'"
				}
			  }';
			}
		$data.=',{
				"type": "spacer",
				"size": "sm"
			  }
			]
		  }
		}
	  }';

	  return $data;
}

function followStep1($head,$user,$message){
	$data='{
		"type": "flex",
		"altText": "Flex Message",
		"contents": {
		  "type": "bubble",
		  "body": {
			"type": "box",
			"layout": "vertical",
			"contents": [
			  {
				"type": "text",
				"text": "เทศบาลตำบลบ้านแดน",
				"weight": "bold",
				"color": "#1DB446",
				"size": "sm"
			  },
			  {
				"type": "text",
				"text": "'.$head.' ",
				"weight": "bold",
				"size": "xxl",
				"margin": "md"
			  },
			  {
				"type": "text",
				"text": "ผู้แจ้งปัญหาคุณ : '.$user.'\nถ้ามีปัญญาเพิ่มเติมสามารถพิมพ์ต่อได้เลยค่ะ",
				"size": "xs",
				"color": "#aaaaaa",
				"wrap": true
			  },
			  {
				"type": "separator",
				"margin": "xxl"
			  },
			  {
				"type": "box",
				"layout": "vertical",
				"margin": "xxl",
				"spacing": "sm",
				"contents": [
				  {
					"type": "box",
					"layout": "vertical",
					"contents": [
					  {
						"type": "text",
						"text": "'.$message.' ",
						"size": "sm",
						"color": "#555555",
						"wrap": true
					  }
					]
				  },
				  {
					"type": "separator",
					"margin": "xxl"
				  },
				  {
					"type": "box",
					"layout": "vertical",
					"contents": [
					  {
						"type": "button",
						"action": {
						  "type": "message",
						  "label": "ยืนยันการแจ้งปัญหา",
						  "text": "PB1OK"
						},
						"style": "primary"
					  },
					  {
						"type": "button",
						"action": {
						  "type": "message",
						  "label": "ยกเลิกการแจ้งปัญหา",
						  "text": "DL"
						}
					  }
					]
				  }
				]
			  }
			]
		  },
		  "styles": {
			"footer": {
			  "separator": true
			}
		  }
		}
	  }';
	return $data;
}

function showAllWork($qCk){
	$rCk=count($qCk);
    $data='{
		"type": "flex",
		"altText": "Flex Message",
		"contents": {
		  "type": "carousel",
		  "contents": [';
	for($i=0;$i<$rCk;$i++)
	{	
		if($i>0){$data.=',';}
		$memName=$qCk[$i]->memName;
		$problemId=$qCk[$i]->problemId;
		$problemAdminStatus=$qCk[$i]->problemAdminStatus;
	$data.='{
			  "type": "bubble",
			  "size": "mega",
			  "hero": {
				"type": "image",
				"url": "https://www.nctsc.com/bandan/images/icon/worker.png",
				"size": "full",
				"aspectMode": "cover",
				"aspectRatio": "320:213"
			  },
			  "body": {
				"type": "box",
				"layout": "vertical",
				"contents": [
				  {
					"type": "text",
					"text": "คุณมีงานที่ได้รับมอบหมาย",
					"weight": "bold",
					"size": "lg",
					"wrap": true
				  },
				  {
					"type": "box",
					"layout": "vertical",
					"contents": [
					  {
						"type": "box",
						"layout": "baseline",
						"spacing": "md",
						"contents": [
						  {
							"type": "text",
							"text": "งานของคุณ'.$memName.'  \nกรุณากดรับงานด้วยค่ะ",
							"wrap": true,
							"color": "#8c8c8c",
							"size": "md"
						  }
						]
					  }
					]
				  }
				],
				"spacing": "sm",
				"paddingAll": "13px"
			  },
			  "footer": {
				"type": "box",
				"layout": "vertical",
				"contents": [';

				if($problemAdminStatus==1)
				{
					$data.='
					{
					  "type": "button",
					  "action": {
						"type": "message",
						"label": "ดูรายละเอียดงาน",
						"text": "ดูรายละเอียดงาน?problemId='.$problemId.'"
					  }
					},
					{
						"type": "button",
						"action": {
						  "type": "message",
						  "label": "ส่งงาน",
						  "text": "ส่งงาน?problemId='.$problemId.'"
						}
					  }';
				}else{
					$data.='
					{
					  "type": "button",
					  "action": {
						"type": "message",
						"label": "ดูรายละเอียดงาน",
						"text": "ดูรายละเอียดงาน?problemId='.$problemId.'"
					  }
					},
					{
					  "type": "button",
					  "action": {
						"type": "message",
						"label": "กดรับงาน",
						"text": "รับงาน?problemId='.$problemId.'"
					  }
					},
					{
					  "type": "button",
					  "action": {
						"type": "message",
						"label": "ไม่รับงาน",
						"text": "ไม่รับงาน?problemId='.$problemId.'"
					  }
					}';
				}

				$data.=']
			  }
			}';
		}
		$data.=']
		}
	  }';
    return $data;
}

function confirm($img,$h1,$bd,$bt1,$txt1,$bt2,$txt2)
{
	$data='{
		"type": "flex",
		"altText": "Flex Message",
		"contents": {
		  "type": "bubble",
		  "hero": {
			"type": "image",
			"url": "'.$img.'",
			"size": "full",
			"aspectRatio": "20:13",
			"aspectMode": "cover"
		  },
		  "body": {
			"type": "box",
			"layout": "vertical",
			"contents": [
			  {
				"type": "text",
				"text": "'.$h1.'",
				"weight": "bold",
				"size": "xl",
				"wrap": true
			  },
			  {
				"type": "box",
				"layout": "vertical",
				"margin": "lg",
				"spacing": "sm",
				"contents": [
				  {
					"type": "box",
					"layout": "baseline",
					"spacing": "md",
					"contents": [
					  {
						"type": "text",
						"text": "'.$bd.'",
						"wrap": true,
						"color": "#666666",
						"size": "md"
					  }
					]
				  }
				]
			  }
			]
		  },
		  "footer": {
			"type": "box",
			"layout": "vertical",
			"spacing": "sm",
			"contents": [
			  {
				"type": "button",
				"style": "link",
				"height": "sm",
				"action": {
				  "type": "message",
				  "label": "'.$bt1.'",
				  "text": "'.$txt1.'"
				}
			  },
			  {
				"type": "button",
				"style": "link",
				"height": "sm",
				"action": {
				  "type": "message",
				  "label": "'.$bt2.'",
				  "text": "'.$txt2.'"
				}
			  },
			  {
				"type": "spacer",
				"size": "sm"
			  }
			]
		  }
		}
	  }';
	return $data;
}

function showLocation($title,$lat,$lng)
{
	$data='{
		"type": "location",
		"title": "'.$title.'",
		"address": "คลิกเพื่อดูแผนที่ค่ะ", 
		"latitude": '.$lat.',
    	"longitude": '.$lng.'
	}';
	return $data;
}

function finishImg($img,$problemId){
	$data='{
		"type": "flex",
		"altText": "Flex Message",
		"contents": {
		  "type": "bubble",
		  "hero": {
			"type": "image",
			"url": "'.$img.'",
			"size": "full",
			"aspectRatio": "20:13",
			"aspectMode": "cover"
		  },
		  "body": {
			"type": "box",
			"layout": "vertical",
			"contents": [
			  {
				"type": "text",
				"text": "อัพโหลดเสร็จสิ้น",
				"weight": "bold",
				"size": "xl"
			  }
			]
		  },
		  "footer": {
			"type": "box",
			"layout": "vertical",
			"spacing": "sm",
			"contents": [
			  {
				"type": "button",
				"style": "link",
				"height": "sm",
				"action": {
				  "type": "message",
				  "label": "ส่งงานเสร็จสิ้น",
				  "text": "ส่งงานเสร็จสิ้น?problemId='.$problemId.'"
				}
			  },
			  {
				"type": "spacer",
				"size": "sm"
			  }
			]
		  }
		}
	  }';
	return $data;
}

function myPoint($problemId,$displayName,$pictureUrl,$problemNote,$memShort){
	$data='{
		"type": "flex",
		"altText": "Flex Message",
		"contents": {
		  "type": "bubble",
		  "hero": {
			"type": "image",
			"url": "'.$pictureUrl.'",
			"size": "full",
			"aspectRatio": "20:13",
			"aspectMode": "cover"
		  },
		  "body": {
			"type": "box",
			"layout": "vertical",
			"contents": [
			  {
				"type": "text",
				"text": "แบบประเมินคุณ : '.$displayName.'",
				"weight": "bold",
				"wrap": true,
				"size": "lg"
			  },
			  {
				"type": "text",
				"text": "การแก้ไข : '.$problemNote.'",
				"weight": "bold",
				"wrap": true,
				"size": "md"
			  },
			  {
				"type": "box",
				"layout": "vertical",
				"margin": "lg",
				"spacing": "sm",
				"contents": [
				  {
					"type": "box",
					"layout": "baseline",
					"spacing": "sm",
					"contents": [
					  {
						"type": "text",
						"text": "งานของคุณได้รับการแก้ไขเรียบร้อยแล้ว กรุณาประเมินงานเพื่อเป็นกำลังใจให้แก่เจ้าหน้าที่ด้วย ขอบคุณค่ะ",
						"wrap": true,
						"color": "#666666",
						"size": "sm"
					  }
					]
				  }
				]
			  }
			]
		  },
		  "footer": {
			"type": "box",
			"layout": "vertical",
			"spacing": "sm",
			"contents": [
			  {
				"type": "button",
				"style": "link",
				"height": "sm",
				"action": {
				  "type": "message",
				  "label": "ดีมาก ( 5 คะแนน)",
				  "text": "แบบประเมิน?PT=5&PID='.$problemId.'&MID='.$memShort.'"
				}
			  },
			  {
				"type": "button",
				"style": "link",
				"height": "sm",
				"action": {
				  "type": "message",
				  "label": "ดี ( 4 คะแนน)",
				  "text": "แบบประเมิน?PT=4&PID='.$problemId.'&MID='.$memShort.'"
				}
			  },
			  {
				"type": "button",
				"style": "link",
				"height": "sm",
				"action": {
				  "type": "message",
				  "label": "พอใช้ ( 3 คะแนน)",
				  "text": "แบบประเมิน?PT=3&PID='.$problemId.'&MID='.$memShort.'"
				}
			  },
			  {
				"type": "button",
				"style": "link",
				"height": "sm",
				"action": {
				  "type": "message",
				  "label": "ควรปรับปรุง ( 2 คะแนน)",
				  "text": "แบบประเมิน?PT=2&PID='.$problemId.'&MID='.$memShort.'"
				}
			  },
			  {
				"type": "button",
				"style": "link",
				"height": "sm",
				"action": {
				  "type": "message",
				  "label": "แย่ ( 1 คะแนน)",
				  "text": "แบบประเมิน?PT=1&PID='.$problemId.'&MID='.$memShort.'"
				}
			  },
			  {
				"type": "spacer",
				"size": "sm"
			  }
			]
		  }
		}
	  }';
	return $data;
}

function aboutUs($q,$page)
{
	
	$r=count($q);

	$data='{
		"type": "flex",
		"altText": "Flex Message",
		"contents": {
		  "type": "carousel",
		  "contents": [';
		for($i=0;$i<$r;$i++)
		{
			if($i>0){$data.=',';}
			$aPic=$page.'/'.$q[$i]->aPic;
			$aHead=$q[$i]->aHead;
			$aBody=$q[$i]->aBody;
			$aFooter=$q[$i]->aFooter;
			$aboutUsDetailId=$q[$i]->aboutUsDetailId;
			$lenBody=mb_strlen($aBody,'UTF-8');
			$u=200;
			
			if($lenBody<$u)
			{
				$x = ($u-$lenBody);
				for($j=0;$j<=$x;$j++){
					$aBody.=" ";
				}
				
			}else{
				
				$aBody=mb_substr($aBody,0,$u,'UTF-8').'...';
			}

			$data.='{
				"type": "bubble",
				"size": "mega",
				"hero": {
				  "type": "image",
				  "url": "'.$aPic.'",
				  "size": "full",
				  "aspectMode": "cover",
				  "aspectRatio": "320:213"
				},
				"body": {
				  "type": "box",
				  "layout": "vertical",
				  "contents": [
					{
					  "type": "text",
					  "text": "'.$aHead.'",
					  "weight": "bold",
					  "size": "xl",
					  "wrap": true
					},
					{
					  "type": "box",
					  "layout": "vertical",
					  "contents": [
						{
						  "type": "box",
						  "layout": "baseline",
						  "spacing": "sm",
						  "contents": [
							{
							  "type": "text",
							  "text": "'.$aBody.'",
							  "wrap": true,
							  "color": "#8c8c8c",
							  "size": "xs"
							}
						  ]
						}
					  ]
					}
				  ],
				  "spacing": "sm",
				  "paddingAll": "13px"
				},
				"footer": {
				  "type": "box",
				  "layout": "vertical",
				  "contents": [
					{
					  "type": "separator",
					  "margin": "xl"
					},
					{
					  "type": "box",
					  "layout": "vertical",
					  "contents": [
						{
						  "type": "text",
						  "text": "'.$aFooter.'",
						  "wrap": true
						},{
							"type": "text",
							"text": "อ่านเพิ่มเติม",
							"action": {
								"type": "message",
								"label": "อ่านเพิ่มเติม",
								"text": "อ่านเพิ่มเติม?SD='.$aboutUsDetailId.'"
							  }
						}
					  ]
					}
				  ]
				}
			  }';
		}
	$data.=']
		}
	  }';
	return $data;
}

function problemTrack($aPic,$aHead,$aBody,$aFooter)
{
	

	$data='{
		"type": "flex",
		"altText": "Flex Message",
		"contents": {
		  "type": "carousel",
		  "contents": [';
		$data.='{
				"type": "bubble",
				"size": "giga",
				"hero": {
				  "type": "image",
				  "url": "'.$aPic.'",
				  "size": "full",
				  "aspectMode": "cover",
				  "aspectRatio": "320:213"
				},
				"body": {
				  "type": "box",
				  "layout": "vertical",
				  "contents": [
					{
					  "type": "text",
					  "text": "'.$aHead.'",
					  "weight": "bold",
					  "size": "xl",
					  "wrap": true
					},
					{
					  "type": "box",
					  "layout": "vertical",
					  "contents": [
						{
						  "type": "box",
						  "layout": "baseline",
						  "spacing": "sm",
						  "contents": [
							{
							  "type": "text",
							  "text": "'.$aBody.'",
							  "wrap": true,
							  "color": "#8c8c8c",
							  "size": "xs"
							}
						  ]
						}
					  ]
					}
				  ],
				  "spacing": "sm",
				  "paddingAll": "13px"
				},
				"footer": {
				  "type": "box",
				  "layout": "vertical",
				  "contents": [
					{
					  "type": "separator",
					  "margin": "xl"
					},
					{
					  "type": "box",
					  "layout": "vertical",
					  "contents": [
						{
						  "type": "text",
						  "text": "'.$aFooter.'",
						  "wrap": true
						}
					  ]
					}
				  ]
				}
			  }';
	$data.=']
		}
	  }';
	return $data;
}

function oldWork($q,$page)
{
	
	$r=count($q);

	$data='{
		"type": "flex",
		"altText": "Flex Message",
		"contents": {
		  "type": "carousel",
		  "contents": [';
		for($i=0;$i<$r;$i++)
		{
			if($i>0){$data.=',';}
			$problemTypeName=$q[$i]->problemTypeName;
			$problemName=$q[$i]->problemName;
			$problemTypeId=$q[$i]->problemTypeId;
			$memName=$q[$i]->memName;
			$problemNote=$q[$i]->problemNote;
			if($problemTypeId==1)
			{
				$aPic=$page.'images/pic/electric.png';
			}else if($problemTypeId==2){
				$aPic=$page.'images/pic/waterSupply.png';
			}else if($problemTypeId==3){
				$aPic=$page.'images/pic/road.png';
			}
			$aHead=$q[$i]->problemName;
			$aBody='ปัญหาของคุณ : '.$memName;
			$aBody.='\nการแก้ไข : '.$problemNote;
			$aFooter='ข้อมูลงานที่ผ่านมาล่าสุด 10 รายการ';
			$data.='{
				"type": "bubble",
				"size": "mega",
				"hero": {
				  "type": "image",
				  "url": "'.$aPic.'",
				  "size": "full",
				  "aspectMode": "cover",
				  "aspectRatio": "320:213"
				},
				"body": {
				  "type": "box",
				  "layout": "vertical",
				  "contents": [
					{
					  "type": "text",
					  "text": "'.$aHead.'",
					  "weight": "bold",
					  "size": "xl",
					  "wrap": true
					},
					{
					  "type": "box",
					  "layout": "vertical",
					  "contents": [
						{
						  "type": "box",
						  "layout": "baseline",
						  "spacing": "sm",
						  "contents": [
							{
							  "type": "text",
							  "text": "'.$aBody.'",
							  "wrap": true,
							  "color": "#8c8c8c",
							  "size": "xs"
							}
						  ]
						}
					  ]
					}
				  ],
				  "spacing": "sm",
				  "paddingAll": "13px"
				},
				"footer": {
				  "type": "box",
				  "layout": "vertical",
				  "contents": [
					{
					  "type": "separator",
					  "margin": "xl"
					},
					{
					  "type": "box",
					  "layout": "vertical",
					  "contents": [
						{
						  "type": "text",
						  "text": "'.$aFooter.'",
						  "wrap": true
						}
					  ]
					}
				  ]
				}
			  }';
		}
	$data.=']
		}
	  }';
	return $data;
}

function shareLo(){
	$data='{
		"type": "flex",
		"altText": "Flex Message",
		"contents": {
		  "type": "carousel",
		  "contents": [
			{
			  "type": "bubble",
			  "size": "mega",
			  "body": {
				"type": "box",
				"layout": "vertical",
				"contents": [
				  {
					"type": "text",
					"text": "กรุณาปักหมุดแผนที่",
					"weight": "bold",
					"size": "xl",
					"wrap": true,
					"color": "#ff0000"
				  },
				  {
					"type": "box",
					"layout": "vertical",
					"contents": [
					  {
						"type": "box",
						"layout": "baseline",
						"spacing": "sm",
						"contents": [
						  {
							"type": "text",
							"text": "กรุณาปักหมุดตำแหน่งของปัญหาในไลน์นี้ โดยการกดปุ่ม + และเลือกตำแหน่ง (Location) เพื่อให้เจ้าหน้าที่เข้าไปแก้ปัญหาให้ด้วยค่ะ",
							"wrap": true,
							"color": "#8c8c8c",
							"size": "lg"
						  }
						]
					  }
					]
				  },
				  {
					"type": "text",
					"text": "ตัวอย่างดังภาพข้างเคียง ->",
					"margin": "xxl",
					"size": "lg"
				  }
				],
				"spacing": "sm",
				"paddingAll": "13px"
			  }
			},
			{
			  "type": "bubble",
			  "size": "mega",
			  "hero": {
				"type": "image",
				"url": "https://nctsc.com/bandan/images/pic/p1.png",
				"size": "full",
				"aspectMode": "fit",
				"aspectRatio": "4:3"
			  },
			  "body": {
				"type": "box",
				"layout": "vertical",
				"contents": [
				  {
					"type": "text",
					"text": "การแชร์ตำแหน่งที่ตั้ง (Location)",
					"weight": "bold",
					"size": "xl",
					"wrap": true
				  },
				  {
					"type": "box",
					"layout": "vertical",
					"contents": [
					  {
						"type": "box",
						"layout": "baseline",
						"spacing": "sm",
						"contents": [
						  {
							"type": "text",
							"text": "1. กดเครื่องหมาย +  เพื่อ หาเมนู แชร์ตำแหน่งที่ตั้งบนไลน์ของท่านเอง",
							"wrap": true,
							"color": "#8c8c8c",
							"size": "lg"
						  }
						]
					  }
					]
				  }
				],
				"spacing": "sm",
				"paddingAll": "13px"
			  }
			},
			{
			  "type": "bubble",
			  "size": "mega",
			  "hero": {
				"type": "image",
				"url": "https://nctsc.com/bandan/images/pic/p2.png",
				"size": "full",
				"aspectMode": "fit",
				"aspectRatio": "4:3"
			  },
			  "body": {
				"type": "box",
				"layout": "vertical",
				"contents": [
				  {
					"type": "text",
					"text": "การแชร์ตำแหน่งที่ตั้ง (Location)",
					"weight": "bold",
					"size": "xl",
					"wrap": true
				  },
				  {
					"type": "box",
					"layout": "vertical",
					"contents": [
					  {
						"type": "box",
						"layout": "baseline",
						"spacing": "sm",
						"contents": [
						  {
							"type": "text",
							"text": "2. เลือกคำว่า แชร์โลเคเชั่น หรือ (share location) บนหน้าไลน์ของท่าน",
							"wrap": true,
							"color": "#8c8c8c",
							"size": "lg"
						  }
						]
					  }
					]
				  }
				],
				"spacing": "sm",
				"paddingAll": "13px"
			  }
			},
			{
			  "type": "bubble",
			  "size": "mega",
			  "hero": {
				"type": "image",
				"url": "https://nctsc.com/bandan/images/pic/p3.png",
				"size": "full",
				"aspectMode": "fit",
				"aspectRatio": "4:3"
			  },
			  "body": {
				"type": "box",
				"layout": "vertical",
				"contents": [
				  {
					"type": "text",
					"text": "การแชร์ตำแหน่งที่ตั้ง (Location)",
					"weight": "bold",
					"size": "xl",
					"wrap": true
				  },
				  {
					"type": "box",
					"layout": "vertical",
					"contents": [
					  {
						"type": "box",
						"layout": "baseline",
						"spacing": "sm",
						"contents": [
						  {
							"type": "text",
							"text": "3. เลือกตำแหน่งที่แจ้งปัญหา แล้วกดปุ่น แชร์ หรือ Share",
							"wrap": true,
							"color": "#8c8c8c",
							"size": "lg"
						  }
						]
					  }
					]
				  }
				],
				"spacing": "sm",
				"paddingAll": "13px"
			  }
			}
		  ]
		}
	  }';
	return $data;
}

?>

