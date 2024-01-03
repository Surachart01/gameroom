<?php 
session_start();


if (!function_exists('hash_equals')) 
{
    defined('USE_MB_STRING') or define('USE_MB_STRING', function_exists('mb_strlen'));
    function hash_equals($knownString, $userString)
    {
        $strlen = function ($string) {
            if (USE_MB_STRING) {
                return mb_strlen($string, '8bit');
            }
            return strlen($string);
        };
        if (($length = $strlen($knownString)) !== $strlen($userString)) {
            return false;
        }
        $diff = 0;
        for ($i = 0; $i < $length; $i++) {
            $diff |= ord($knownString[$i]) ^ ord($userString[$i]);
        }
        return $diff === 0;
    }
}
class ncts
{
	
	private $channelAccessToken ; 
	private $channelSecret;
	private $nCompany;

	/* private $hn="localhost";
	private $un="root";
	private $pn="root";
	private $dn="baandan"; */

	private $hn="localhost";
	private $un="root";
	private $pn="12345678";
	private $dn="nctsc_streamimg";

	public function __construct($channelAccessToken,$channelSecret,$nCompany) 
	{
			
		$this->channelAccessToken = $channelAccessToken;
		$this->channelSecret = $channelSecret;
		$this->nCompany = $nCompany;
    }

	
	private function conn()
	{
		$con = new mysqli($this->hn,$this->un,$this->pn,$this->dn);
		$con->set_charset("utf8mb4");
		return $con;		
	}

	public function getNCompany($nCompany){
		$sql = "select * from company where nCompany='$nCompany'";
		$q = $this->seljson($sql);
		$r = count($q);
		if($r=='0')
		{
			echo "<script>alert('กรุณาติดต่อผู้ดูแลระบบก่อนใช้งานค่ะ')</script>";
			exit();
		}else{
			return $q;
		}
	}

	public function query($sql)
	{
			$q=mysqli_query($this->conn(),$sql);
			if($q)
			{
				return $q;
			}	
	}

	public function ses()
	{
		if(!isset($_SESSION["nCom"]))
		{
			$this->ckCookie();

		}else{
			echo "<script>window.location='index.php'</script>";
			
		}
	}

	public function saveLog($adminId,$memId,$txt){
		$sql = "insert into log(adminId,memId,detail) values('$adminId','$memId','$txt')";
		$q = $this->inUp($sql);
	}

	public function ckCookie()
	{
		if (isset($_COOKIE["my"])) 
		{
			$memId = $_COOKIE["my"]; 
			$nCompany = $this->nCompany;
			$sql = "select * from member where memId='$memId' and nCompany = '$nCompany'";
			$q = $this->selJson($sql);
			$_SESSION["iUser"]=$q;
			$x = json_decode($q);
			$_SESSION["nCom"] = $x[0]->nCompany;
			echo "<script>window.location='index.php'</script>";
		}
	}

	public function selJson($sql)
	{		
		$q=$this->query($sql);
		$arr=array();
		$arrField=array();
		$num=mysqli_num_rows($q);
		while($f=mysqli_fetch_field($q)) 
		{
			array_push($arrField,$f->name); 
		}	
		
		$numField=count($arrField);
		
		while($data=mysqli_fetch_array($q)) 
		{
			$arrCol=array();
			for($i=0;$i<$numField;$i++)
			{
				$arrCol[$arrField[$i]]=$data[$i]; 
			}
			array_push($arr,$arrCol);	
		}
				
		return json_encode($arr);
	}
	

    public function inUp($sql){
        $q=mysqli_query($this->conn(),$sql);
		if($q)
		{
			return 1;
		}else{
            return 0;
        }
    }
	
    public function delete($tb,$pk)
	{
		
		$i=0;
		$wh;
		foreach($_POST as $key=>$val)
		{
			
			if($key==$pk){
				$wh= $key . "='" . $val ."'";
			}
			
		}
	    $sql="delete from $tb where $wh";
		$q=$this->query($sql);
		if($q)
		{
			return 1;
		}else{
			return 0;
		}
	}

	public function sel($sql)
	{

		$q = $this->query($sql);
		$num= mysqli_num_rows($q);
		$arr=array();		

		for($i=0;$i<$num;$i++)
		{
			$data=mysqli_fetch_object($q);
			$arr[$i]=$data;
		}

		return $arr;		
	}
	

	public function insert($tb,$ar)
	{
		$field;
		$values;
		$i=0;
		$num=count($_POST);
		foreach($_POST as $key=>$val)
		{
			$ck=0;
			foreach($ar as $k=>$v){
				if($key==$k)
				{
					$ck++;	
				}
			}
			if($ck==0)
			{
				if($key != "button")
				{
					if($i==0)
					{
						$field.=$key;
						$values.="'". $val . "'" ;
					}else{
						$field.="," . $key;
						$values.= "," ."'". $val . "'" ;
							
					}
					$i++;
				}
			}
			
		}
        $num=count($ar);
        
        if($ar>0){
            foreach($ar as $key=>$val){
                $field.="," . $key;
				$values.= "," ."'". $val . "'" ;
				
            }
        }
		$sql="insert into $tb($field) values($values)";
       
		$q=$this->query($sql);
		if($q)
		{
			return 1;
		}else{
			return 0;	
		}
	}

	public function update($tb,$ar,$pk)
	{
		
		$field;
		$values;
		$wh;
		$i=0;
		$num=count($_POST);
		foreach($_POST as $key=>$val)
		{
			$ck=0;
			foreach($ar as $k=>$v){
				if($key==$k)
				{
					$ck++;	
				}
			}
			if($ck==0)
			{
				if($key != "button" && $key != "$pk")
				{
					if($i==0)
					{
						$field.= $key . "='" . $val . "'";	
						
					}else{
						
						$field.= "," . $key . "='" . $val . "'";	
					}
					$i++;
				}
			}
			
		}
		
		$num=count($ar);
        
        if($ar>0){
            foreach($ar as $key=>$val){
				if($i==0)
				{
					$field.= $key . "='" . $val . "'";
					$i++;
				}else{
					$field.= "," . $key . "='" . $val . "'";
				}
                
            }
        }
		
		foreach($_POST as $key=>$val)
		{
			if($key==$pk){
				$wh= $key . "='" . $val ."'";
			}
		}
		
		echo $sql="update $tb set $field where $wh";
        
		$q=$this->query($sql);
		if($q)
		{
			return 1;
		}else{
			return 0;
		}
	}

	
	
	public function ckPage($var,$page)
	{
		if($var=="")
		{
			$this->msg("กรุณาเข้าหน้าต่างให้ถูกต้องด้วยค่ะ",$page);
			exit();
		}
	}

	public function adminOnly($var,$page)
	{
		if($var=='1' || $var=='9')
		{
			
		}else{
			$this->msg("หน้านี้สำหรับผู้ดูแลระบบค่ะ",$page);
			exit();
		}
	}

	public function msg($msg,$page)
	{
		echo " <script>alert('$msg');window.location='$page';</script>";
		exit();
	}
	
	public function rePage($var,$page)
	{
		if($var=="")
		{
			echo "<script>window.location='" . $page . "';</script>";
		}
	}
	
	public function havePage($var,$page){
		if($var != ""){
			echo "<script>window.location='" . $page . "';</script>";
		}
	}

	public function checkAdmin($userId)
	{
		$sql = "select status from member where memId='$userId' and nCompany='$this->nCompany'";
		$q = $this->sel($sql);
		$status = $q[0]->status;
		if($status == 1 || $status == 9 || $status==2)
		{
			return true;
		}
		exit();
	}

	public function checkUser($userId)
	{
		$sql = "select status from member where memId='$userId' and nCompany='$this->nCompany'";
		$q = $this->sel($sql);
		$status = $q[0]->status;
		if($status == 0)
		{
			return true;
		}
		exit();
	}

	public function changeMenu($userId)
	{
		$sqlStatus="select * from member where mId='$userId'";
		$qStatus=$this->sel($sqlStatus);
		$st=$qStatus[0]->status;
		$me=$qStatus[0]->iMenu;

		if($st==1 || $st==2 || $st==9){
			if($me!=1)
			{
				$response=$this->setMenu($userId,1);
				$sqlUp="update member set iMenu='1' where mId='$userId'";
				$qUp=$this->inUp($sqlUp);
			}
		}else if($st==0){
			if($me!=2)
			{
				$response=$this->setMenu($userId,2);
				$sqlUp="update member set iMenu='2' where mId='$userId'";
				$qUp=$this->inUp($sqlUp);
			}
		}
	}
	public function checkGameRoom($groupId,$replyToken)
	{
		$sql = "select game.gameName,gameRoom.gameId,gameRoom.gameRoomId,gameRoom.gameRoomStatus,gameRoom.roomId,gameRoom.limitRow,gameRoom.limitUser,gameRoom.limitMin,gameRoom.vip,gameRoom.gamePay,gameRoom.extra,gameRoom.extraCount from game,gameRoom where game.gameIdShot=gameRoom.gameId and gameRoom.roomId='$groupId' and gameRoom.gameRoomEnd='0' and game.nCompany='$this->nCompany'";
		$q = $this->sel($sql);
		$r = count($q);
		if($r==0)
		{
			$this->replyMsg($replyToken,'ห้องนี้ยังไม่มีเกมส์ค่ะ');
			exit();

		}else{
			return $q;
		}
		
	}

	public function checkGameRoomUser($groupId,$replyToken)
	{
		$sql = "select game.gameName,gameRoom.gameId,gameRoom.gameRoomId,gameRoom.gameRoomStatus,gameRoom.roomId,gameRoom.limitRow,gameRoom.limitUser,gameRoom.limitMin,gameRoom.vip,gameRoom.gamePay,gameRoom.extra,gameRoom.extraCount from game,gameRoom where game.gameIdShot=gameRoom.gameId and gameRoom.roomId='$groupId' and gameRoom.gameRoomEnd='0' and game.nCompany='$this->nCompany'";
		$q = $this->sel($sql);
		$r = count($q);
		if($r==0)
		{
			
			exit();

		}else{
			return $q;
		}
		
	}

	public function blacklist($userId)
	{
		$sql = "select status from member where memId='$userId' and nCompany='$this->nCompany'";
		$q = $this->sel($sql);
		$status=$q[0]->status;
		if($status=='4')
		{
			exit();
		}
		
	}

	public function checkGameStatus($groupId,$replyToken,$gameRoomStatus,$gameRoomThai)
	{
		$sql = "select gameRoomStatus from gameRoom where gameRoomStatus = '$gameRoomStatus' and roomId='$groupId' and gameRoomEnd='0' and nCompany='$this->nCompany'";
		$q = $this->sel($sql);
		$r = count($q);
		if($r>0)
		{
			$txt = 'เกมส์นี้อยู่ในสถานะ '.$gameRoomThai.' อยู่แล้วค่ะ';
			$this->replyMsg($replyToken,$txt);
			exit();
		}
		return true;
	}

	public function isGameStatus($groupId)
	{
		$sql = "select gameRoomStatus from gameRoom where roomId='$groupId' and gameRoomEnd='0' and nCompany='$this->nCompany'";
		$q = $this->sel($sql);
		$r = count($q);
		if($r>0)
		{
			return $q[0]->gameRoomStatus;
			exit();
		}else{
			$txt = 'ไม่มีเกมส์'.$gameRoomThai.' อยู่ในห้องนี้แล้วค่ะ';
			$this->replyMsg($replyToken,$txt);
			exit();
		}
		
	}

	
	
	public function  getUserProfiles($memId) {
		$token = $this->channelAccessToken;
		$url = "https://api.line.me/v2/bot/profile/" . $memId;
		$header = array(
				  "Content-Type: application/json",
				  'Authorization: Bearer ' . $token,
			  );
			$ch = curl_init();
		  curl_setopt($ch, CURLOPT_HEADER, 0);
		  curl_setopt($ch, CURLOPT_VERBOSE, 1);
		  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		  curl_setopt($ch, CURLOPT_FAILONERROR, 0);
		  curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		  curl_setopt($ch, CURLOPT_URL, $url);
		  
		  try {
			  $returned =  curl_exec($ch);
			  $re = json_decode($returned);
			  $displayName = $re->displayName;
			  $pictureUrl = $re->pictureUrl;
			  $statusMessage = $re->statusMessage;
			  if($displayName=="")
			  {
				  $displayName = 'User Leave Chat Room';
				  $pictureUrl = 'https://www.nctsc.com/nctsLineChatBot/img/userBlock.png';
				  $statusMessage = '';
			  }
		  } catch (Exception $e) {
			  $displayName = 'User Leave Chat Room';
			  $pictureUrl = 'https://www.nctsc.com/nctsLineChatBot/img/userBlock.png';
			  $statusMessage = '';
		  }  
			return array($displayName,$pictureUrl,$statusMessage);
	}

	public function get_user_id($memID)
	{
		 $sql = "SELECT memId FROM member WHERE memID = '$memID' and nCompany='$this->nCompany'";
		 $q = $this->sel($sql);
		 return $q[0]->memId;
	}

	
	public function chDate($tp){
		$tp = explode(" ",$tp);
		$date = explode("-",$tp[0]);
		$t = $tp[1];
		$m;
		switch($date[1]){
			case "01" : $m="JAN";
			break;
			case "02" : $m="FEB";
			break;
			case "03" : $m="MAR";
			break;
			case "04" : $m="APR";
			break;
			case "05" : $m="MAY";
			break;
			case "06" : $m="JUN";
			break;
			case "07" : $m="JLY";
			break;
			case "08" : $m="AUG";
			break;
			case "09" : $m="SEP";
			break;
			case "10" : $m="OCT";
			break;
			case "11" : $m="NOV";
			break;
			case "12" : $m="DEC";
			break;
			default : $m="JAN";
			break;
		}
		$today = date("Y-m-d");
		if($today == $tp[0])
		{
			return "today " . $t;
		}else{
			return $d = $date[2] . " " . $m . " " . $date[0] . " " . $t;
		}
		
	}

	public function chDateThai($tp){
		$tp = explode(" ",$tp);
		$date = explode("-",$tp[0]);
		$t = $tp[1];
		$m;
		switch($date[1]){
			case "01" : $m="ม.ค.";
			break;
			case "02" : $m="ก.พ.";
			break;
			case "03" : $m="มี.ย.";
			break;
			case "04" : $m="เม.ย.";
			break;
			case "05" : $m="พ.ต.";
			break;
			case "06" : $m="มิ.ย.";
			break;
			case "07" : $m="ก.ค.";
			break;
			case "08" : $m="ส.ค.";
			break;
			case "09" : $m="ก.ย.";
			break;
			case "10" : $m="ต.ค.";
			break;
			case "11" : $m="พ.ย.";
			break;
			case "12" : $m="ธ.ค.";
			break;
			default : $m="ม.ค.";
			break;
		}
		
			return $d = $date[2] . " " . $m . " " . $date[0] . " " . $t;

		
		//return $tp;
	}

	public function insertMember($userId,$displayName,$pictureUrl,$nCompany,$date){
		if($userId=="")
		{
			return false;
			exit();
		}
		$sql = "select * from member where mId='$userId'";
		$q = $this->sel($sql);
		$r = count($q);
		if($r==0)
		{
			$sql1="select * from member";
			$q1=$this->sel($sql1);
			$r1=count($q1);
			$rp=intval($r1)+1;
			$a="G";
			$b="00000";
			$c=strlen($rp);
			$d=substr($b,$c);
			$memShort=$a.$d.$rp;
			$memId = md5($memShort.$date);
			$sql = "insert into member(memId,mId,memShort,memName,status,memPic,nCompany) values('$memId','$userId','$memShort','$displayName','0','$pictureUrl','$nCompany')";
			$q = $this->inUp($sql);
		}
		return $sql;
	}
	
    public function fetchData()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            error_log("Method not allowed");
            exit();
        }
        $entityBody = file_get_contents('php://input');
        if (strlen($entityBody) === 0) {
            http_response_code(400);
            error_log("Missing request body");
            exit();
        }
        if (!hash_equals($this->sign($entityBody), $_SERVER['HTTP_X_LINE_SIGNATURE'])) {
            http_response_code(400);
            error_log("Invalid signature value");
            exit();
        }
        $data = json_decode($entityBody, true);
        if (!isset($data['events'])) {
            http_response_code(400);
            error_log("Invalid request body: missing events property");
            exit();
        }
		file_put_contents('log.txt', file_get_contents('php://input') . PHP_EOL, FILE_APPEND);
        return $data;
    }

	public function backupMyDb()
	{
		$command='mysqldump --opt -h' .$this->hn .' -u' .$this->un .' -p' .$this->pn .' ' .$this->dn .' > ' .$this->en;
		exec($command,$output,$worked);
		switch($worked)
		{
			case 0: return 0;
			break;
			case 1: return 1; 
			break;
			default: return 1;
			break;
		}
		return 1;
	}



  
	public function replyMsg($replyToken,$msg){
			$data = '{
				"type" : "text",
				"text" : "'.$msg.'"	
			}';

			$fData = json_decode($data);
			$messages['replyToken'] = $replyToken;
			$messages['messages'][] = $fData;
			$ms = json_encode($messages);
			
			
			$header = array(
				"Content-Type: application/json",
				'Authorization: Bearer ' . $this->channelAccessToken,
			);
			
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://api.line.me/v2/bot/message/reply',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $ms,
				CURLOPT_HTTPHEADER => $header
			));
			
			$returned =  curl_exec($curl);
			$httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
			$err = curl_error($curl);
			curl_close($curl);
			return true;

	}

	public function replyFlexMsg($replyToken,$fData){
	
		$messages['replyToken'] = $replyToken;
		$messages['messages'][] = $fData;
		$ms = json_encode($messages);
				
		$header = array(
			"Content-Type: application/json",
			'Authorization: Bearer ' . $this->channelAccessToken,
		);
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://api.line.me/v2/bot/message/reply',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $ms,
			CURLOPT_HTTPHEADER => $header
		));
		
		$returned =  curl_exec($curl);
		$httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
		$err = curl_error($curl);
		curl_close($curl);
		return $true;

	}

	public function replyFlexMsg2($replyToken,$fData,$fData2){

		
		$messages['replyToken'] = $replyToken;
		$messages['messages'][] = $fData;
		$messages['messages'][] = $fData2;
	
		$ms = json_encode($messages);
		
		
		$header = array(
			"Content-Type: application/json",
			'Authorization: Bearer ' . $this->channelAccessToken,
		);
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://api.line.me/v2/bot/message/reply',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $ms,
			CURLOPT_HTTPHEADER => $header
		));
		
		$returned =  curl_exec($curl);
		$httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
		$err = curl_error($curl);
		curl_close($curl);
		return $true;
	
	}

	public function replyFlexMsg3($replyToken,$fData,$fData2,$fData3){

		
		$messages['replyToken'] = $replyToken;
		$messages['messages'][] = $fData;
		$messages['messages'][] = $fData2;
		$messages['messages'][] = $fData3;
	
		$ms = json_encode($messages);
		
		
		$header = array(
			"Content-Type: application/json",
			'Authorization: Bearer ' . $this->channelAccessToken,
		);
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://api.line.me/v2/bot/message/reply',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $ms,
			CURLOPT_HTTPHEADER => $header
		));
		
		$returned =  curl_exec($curl);
		$httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
		$err = curl_error($curl);
		curl_close($curl);
		return $true;
	
	}

	public function replyFlexMsg4($replyToken,$fData,$fData2,$fData3,$fData4){

			
		$messages['replyToken'] = $replyToken;
		$messages['messages'][] = $fData;
		$messages['messages'][] = $fData2;
		$messages['messages'][] = $fData3;
		$messages['messages'][] = $fData4;
		$ms = json_encode($messages);
		
		
		$header = array(
			"Content-Type: application/json",
			'Authorization: Bearer ' . $this->channelAccessToken,
		);
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://api.line.me/v2/bot/message/reply',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $ms,
			CURLOPT_HTTPHEADER => $header
		));
		
		$returned =  curl_exec($curl);
		$httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
		$err = curl_error($curl);
		curl_close($curl);
		return $true;

	}
	public function replyFlexMsg5($replyToken,$fData,$fData2,$fData3,$fData4,$fData5){

			
		$messages['replyToken'] = $replyToken;
		$messages['messages'][] = $fData;
		$messages['messages'][] = $fData2;
		$messages['messages'][] = $fData3;
		$messages['messages'][] = $fData4;
		$messages['messages'][] = $fData5;
		$ms = json_encode($messages);
		
		
		$header = array(
			"Content-Type: application/json",
			'Authorization: Bearer ' . $this->channelAccessToken,
		);
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://api.line.me/v2/bot/message/reply',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $ms,
			CURLOPT_HTTPHEADER => $header
		));
		
		$returned =  curl_exec($curl);
		$httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
		$err = curl_error($curl);
		curl_close($curl);
		return $true;

	}






	public function pushMsg($userId,$msg)
	{	
			$data ='{
				"type":"text",
				"text" : "'.$msg.'"
			}';
			

			$fData = json_decode($data);
			$m['to'] = $userId;
			$m['messages'][] = $fData;
			$ms = json_encode($m);

      		$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://api.line.me/v2/bot/message/push',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 40,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $ms,
				CURLOPT_HTTPHEADER => array(
					"authorization: Bearer ".$this->channelAccessToken,
					"cache-control: no-cache",
					"content-type: application/json; charset=UTF-8",
				)
			));

		$returned =  curl_exec($curl);
		$httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
		$err = curl_error($curl);
		curl_close($curl);
		return $httpCode;

	}

	public function pushFlexMsg($userId,$fData)
	{	
			
			$m['to'] = $userId;
			$m['messages'][] = $fData;
			$ms = json_encode($m);

      		$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://api.line.me/v2/bot/message/push',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 40,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $ms,
				CURLOPT_HTTPHEADER => array(
					"authorization: Bearer ".$this->channelAccessToken,
					"cache-control: no-cache",
					"content-type: application/json; charset=UTF-8",
				)
			));

		$returned =  curl_exec($curl);
		$httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
		$err = curl_error($curl);
		curl_close($curl);
		return $httpCode;

	}

	public function sendMultiCast($msg,$arr){
		$data ='{
			"type":"text",
			"text" : "'.$msg.'"
		}';
		$r=count($arr);
		for($i=0;$i<$r;$i++)
		{
			$m['to'][] = $arr[$i];
		}
		/* $m['to'][] = $this->adminId;
		$m['to'][] = $this->adminId2;
		$m['to'][] = $this->adminId3;
		$m['to'][] = $this->adminId4; */

		$fData = json_decode($data);
		$m['messages'][] = $fData;
		$ms1 = json_encode($m);
		$curl = curl_init();
          curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.line.me/v2/bot/message/multicast',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 40,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $ms1,
          CURLOPT_HTTPHEADER => array(
            "authorization: Bearer ".$this->channelAccessToken,
            "cache-control: no-cache",
            "content-type: application/json; charset=UTF-8",
          ),
        ));

       
	   $returned =  curl_exec($curl);
	   $httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
       $err = curl_error($curl);
	   curl_close($curl);
	   return $httpCode;
	}


	public function sendFlexMultiCast($fData,$arr){
		
		$r=count($arr);
		for($i=0;$i<$r;$i++)
		{
			$m['to'][] = $arr[$i];
		}
		/* $m['to'][] = $this->adminId;
		$m['to'][] = $this->adminId2;
		$m['to'][] = $this->adminId3;
		$m['to'][] = $this->adminId4; */

		$m['messages'][] = $fData;
		$ms1 = json_encode($m);
		$curl = curl_init();
          curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.line.me/v2/bot/message/multicast',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 40,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $ms1,
          CURLOPT_HTTPHEADER => array(
            "authorization: Bearer ".$this->channelAccessToken,
            "cache-control: no-cache",
            "content-type: application/json; charset=UTF-8",
          ),
        ));

       
	   $returned =  curl_exec($curl);
	   $httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
       $err = curl_error($curl);
	   curl_close($curl);
	   return $httpCode;
	}

	public function leaveGroup($replyToken,$groupId){

		$url = 'https://api.line.me/v2/bot/group/'.$groupId.'/leave';
		
		$data = '{
			"type" : "text",
			"text" : "LEAVE"	
		}';

		$fData = json_decode($data);
		$messages['replyToken'] = $replyToken;
		$messages['messages'][] = $fData;
		$ms = json_encode($messages);
		

			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 40,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $ms,
				CURLOPT_HTTPHEADER => array(
					"authorization: Bearer ".$this->channelAccessToken,
					"cache-control: no-cache",
					"content-type: application/json; charset=UTF-8",
				)
			));

		$returned =  curl_exec($curl);
		$httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
		$err = curl_error($curl);
		curl_close($curl);
		return $httpCode;
	}

	public function setMenu($userId,$status){
		if($status==1)
		{
			$richMenuID = 'richmenu-86a18eb93e97e6e3d2465e9aa692c822'; //admin
		}else{
			$richMenuID = 'richmenu-100f16d1f2c05418ec83de3951c714e2'; // user
		}
		
		$url = "https://api.line.me/v2/bot/user/$userId/richmenu/$richMenuID";

		$data = '{
			"type" : "text",
			"text" : " "	
		}';

		$fData = json_decode($data);
		$messages['replyToken'] = $replyToken;
		$messages['messages'][] = $fData;
		$ms = json_encode($messages);
		

			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 40,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $ms,
				CURLOPT_HTTPHEADER => array(
					"authorization: Bearer ".$this->channelAccessToken,
					"cache-control: no-cache",
					"content-type: application/json; charset=UTF-8",
				)
			));

		$returned =  curl_exec($curl);
		$httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
		$err = curl_error($curl);
		curl_close($curl);
		return $httpCode;
		
	}
  
    public function pushMessage($message) 
    {
        
   	 $response = exec_url('https://api.line.me/v2/bot/message/push',$this->channelAccessToken,json_encode($message));
       
    }
  
    public function profil($userId)
    {
      
    return json_decode(exec_get('https://api.line.me/v2/bot/profile/'.$userId,$this->channelAccessToken));
       
    }

    public function cont($messageid)
    {
      
    return json_decode(exec_get('https://api.line.me/v2/message/'.$messageid.'/content',$this->channelAccessToken));
       
    }


	public function replyMessage($message)
    {
        $header = array(
            "Content-Type: application/json",
            'Authorization: Bearer ' . $this->channelAccessToken,
        );
        $context = stream_context_create(array(
            "http" => array(
                "method" => "POST",
                "header" => implode("\r\n", $header),
                "content" => json_encode($message),
            ),
        ));
    	$response = exec_url('https://api.line.me/v2/bot/message/reply',$this->channelAccessToken,json_encode($message));
    }

	public function sentMulticast($ms)
	{
		$datasReturn = [];
      	$curl = curl_init();
          curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.line.me/v2/bot/message/multicast',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 40,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $ms,
          CURLOPT_HTTPHEADER => array(
            "authorization: Bearer ".$this->channelAccessToken,
            "cache-control: no-cache",
            "content-type: application/json; charset=UTF-8",
          ),
        ));

       
	   $returned =  curl_exec($curl);
	   $httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
       $err = curl_error($curl);
	   curl_close($curl);
	    return $httpCode;
	   
	}

	

    private function sign($body)
    {
        $hash = hash_hmac('sha256', $body, $this->channelSecret, true);
        $signature = base64_encode($hash);
        return $signature;
    }


}

function sendPost($channelAccessToken,$url,$msg)
{
	$header = array(
		"Content-Type: application/json",
		'Authorization: Bearer '.$channelAccessToken,
	);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_POST,           1 );
	curl_setopt($ch, CURLOPT_POSTFIELDS,     $msg); 
	curl_setopt($ch, CURLOPT_FAILONERROR, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_URL, $url);

	$returned =  curl_exec($ch);

	return($returned);
}


function exec_get($fullurl,$channelAccessToken)
{
    
    $header = array(
            "Content-Type: application/json",
            'Authorization: Bearer '.$channelAccessToken,
        );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);    
    curl_setopt($ch, CURLOPT_FAILONERROR, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_URL, $fullurl);
    
    $returned =  curl_exec($ch);
  
    return($returned);
}
function exec_url($fullurl,$channelAccessToken,$message)
{
    
    $header = array(
            "Content-Type: application/json",
            'Authorization: Bearer '.$channelAccessToken,
        );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_POST,           1 );
    curl_setopt($ch, CURLOPT_POSTFIELDS,     $message); 
    curl_setopt($ch, CURLOPT_FAILONERROR, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_URL, $fullurl);
    
    $returned =  curl_exec($ch);
  
    return($returned);
}

function exec_url_aja($fullurl)
  {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_VERBOSE, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_FAILONERROR, 0);
      curl_setopt($ch, CURLOPT_URL, $fullurl);
      
      $returned =  curl_exec($ch);
    
      return($returned);
  }
  

  ?>