<?php  
       session_start();
       include("include/nctsClass.php");
       include("include/xml.php");
       date_default_timezone_set("Asia/Bangkok");
       $nctsXML = new SimpleXMLElement($xmlstr);
       $nCompany = $nctsXML->ncts[0]->genId->nCompany;;
       $channelAccessToken = $nctsXML->ncts[0]->genId->channelAccessToken;
       $channelSecret = $nctsXML->ncts[0]->genId->channelSecret;
       $liffId = $nctsXML->ncts[0]->genId->liffId;
       $page = $nctsXML->ncts[0]->genId->page;
       $firstAdmin = $nctsXML->ncts[0]->genId->firstAdmin;
       $n = new ncts("","","");
       $date = date("Y-m-d H:i:s");
   
       foreach($_POST as $key=>$val){
           $$key = $val;
       }
   
       $sqlSel = "SELECT * FROM member WHERE email='$email'";
       $qSel = $n->sel($sqlSel);
   
       $rSel = count($qSel);
       
       if($rSel == 1){
            $sqlUp = "UPDATE member SET gId='$gId' WHERE email = '$email'";
            $qUp = $n->inUp($sqlUp);
            if($qUp == 1){
                $_SESSION['iUser'] = $qSel;
                echo json_encode("1");
            }else{
                echo json_encode("0");
            }
        }
        if($rSel == 0){
            
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
            $sqlIn = "INSERT INTO member (memId,gId,memShort,nickName,email,memPic,memberDate,nCompany) VALUES          
            ('$memId','$gId','$memShort','$fullname','$email','$pictureUrl','$date','$nCompany')";
            $qIn = $n->inUp($sqlIn);
            if($qIn == 1){
                $qSel2 = $n->sel($sqlSel);
                $_SESSION['iUser'] = $qSel2;
                echo json_encode($qIn);
            }else{
                echo json_encode("hi");
            }
        }
        if($rSel >1){
            echo json_encode("2");
        }
       

?>