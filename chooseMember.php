<?php
session_start();
include('include/nctsClass.php');
$n = new txml();
$u = json_decode($_SESSION['iUser']);
$nCompany = $u[0]->nCompany;
$xml = $n->chxml($nCompany);
include('include/xml_'.$xml.'.php');
date_default_timezone_set("Asia/Bangkok");
$nctsXML = new SimpleXMLElement($xmlstr);
$nCompany = $nctsXML->ncts[0]->genId->nCompany;;
$channelAccessToken = $nctsXML->ncts[0]->genId->channelAccessToken;
$channelSecret = $nctsXML->ncts[0]->genId->channelSecret;
$liffId = $nctsXML->ncts[0]->genId->liffId;
$n = new ncts($channelAccessToken,$channelSecret,$nCompany);
$status = $u[0]->status;
$n->adminOnly($status,'index.php');
$sta=0;
if(isset($_POST['sta'])){$sta=$_POST['sta'];}
$start = $_POST['start'];
$stop = $_POST['stop'];
$s = 100;
if($start==""){
    $start=0;
    $stop=$s;
}


    $sql = "select * from member where status='$sta' and status<>4 and status<>9 and nCompany='$nCompany' order by memShort DESC  LIMIT $start,$s";

if(isset($_POST['txt']) && $_POST['txt']!=''){
    $txt = $_POST['txt'];
    $sql = "select * from member where memName like '%$txt%' and status='$sta' and status<>4 and status<>9 and nCompany='$nCompany' order by memShort DESC   LIMIT $start,$s";
}



$q = $n->sel($sql);
$r = count($q);
$sql1 = "select * from member wherestatus='$sta' and status<>4 and status<>9 and nCompany='$nCompany'";
$q1 = $n->sel($sql1);
$r1 = count($q1);
$st = ceil($r1/$s);


?>

<div class="card">
    <div class="card-header">
        <h5>ตารางสมาชิก</h5>
        <span>ค้นหาสมาชิก <input type="text" id="search" name="search" class="inpSearch"/></span>
        <input type="hidden" id="sta" name ="sta" value="<?php echo $sta;?>"/>
        <div class="card-header-right">  </div>
    </div>
        <div class="card-block table-border-style">
            <div class="table-responsive" id="searchName">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ชื่อสมาชิก-สกุล</th>
                            <th>รหัสสมาชิก</th>
                            <th>รูปสมาชิก</th>
                            <th>เบอร์โทรศัพท์</th>
                            <?php
                                if($status==9)
                                {
                                    echo '<th>เปลี่ยนสถานะ</th>
                                    <th>เพิ่มสต๊าฟ</th>';
                                }
                            ?>

                            <th>แก้ไขข้อมูล</th>
                            <th>ข้อมูลการทำงาน</th>
                            <th>ลบ</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $stm=$start;
                    for($i=0;$i<$r;$i++)
                    {
                        
                        $sta = $q[$i]->status;
                        $st1='&nbsp;';
                        $bt1='&nbsp;';
                        if($status==9 || $status==1)
                        {
                            if($sta==0){
                                $st1='<td><button id="btAddAdmin" data-param="'.$q[$i]->memId.',1,'.$q[$i]->memName.'" class="btn btn-success chSta" ><i class="ti-crown"></i></button></td>';
                                $btStaff='<td><button id="btAddAdmin" data-param="'.$q[$i]->memId.',2,'.$q[$i]->memName.'" class="btn btn-active chSta" ><i class="ti-id-badge"></i></button></td>';
                            }else if($sta==1){
                                $st1='<td><button id="btAddAdmin" data-param="'.$q[$i]->memId.',0,'.$q[$i]->memName.'" class="btn btn-info chSta" ><i class="ti-user"></i></button></td>';
                                $btStaff='<td><button id="btAddAdmin" data-param="'.$q[$i]->memId.',2,'.$q[$i]->memName.'" class="btn btn-active chSta" ><i class="ti-id-badge"></i></button></td>';
                            }else if($sta==2){
                                $st1='<td><button id="btAddAdmin" data-param="'.$q[$i]->memId.',1,'.$q[$i]->memName.'" class="btn btn-success chSta" ><i class="ti-crown"></i></button></td>';
                                $btStaff='<td><button id="btAddAdmin" data-param="'.$q[$i]->memId.',0,'.$q[$i]->memName.'" class="btn btn-warning  chSta" ><i class="ti-id-badge"></i></button></td>';
                            }
                            
                            //$bt1='<td><button id="btInsertCash" data-param="'.$q[$i]->memId.'" class="btn btn-info insertCash"  ><i class="ti-money"></i></button></td>';
                        }
                        
                            echo '<tr>
                            <th scope="row">'.($stm+=1).'</th>
                            <td>'.$q[$i]->memName.' '.$q[$i]->memSur.'</td>
                            <td>'.$q[$i]->memShort.'</td>
                            <td><img src="'.$q[$i]->memPic.'" width="30px" /></td>
                            <td>'.$q[$i]->tel.'</td>'
                            .$st1.$btStaff.
                            '<td><button id="btEditMember" data-param="'.$q[$i]->memId.'" class="btn btn-info editMember"  ><i class="ti-pencil-alt"></i></button></td>
                            <td><a href="#" onclick="$.address.value(\'/showMemberStat?memId='.$q[$i]->memId.'\'); return false;"><button id="btMemberStat'.$i.'" class="btn btn-inverse"  ><i class="ti-stats-up"></i></button></a></td>
                            <td><button id="btDelMember" data-param="'.$q[$i]->memId.',4,'.$q[$i]->memName.'" class="btn btn-danger chSta"  ><i class="ti-trash"></i></button></td>
                            </tr>';  
                    }
                    ?>
                    </tbody>
                </table>
                <?php   
                    $sta1 = 0;
                    $stp1 = $s;
                    for($i=0;$i<$st;$i++)
                    {
                        $co='#000000';
                        if($start==$sta1){
                            $co='#0d4ad5';
                        }
                        
                        echo '<span style="color:'.$co.'">[<a href="#" onclick="$.address.value(\'/chooseMember?start='.$sta1.'&amp;stop='.$stp1.'\'); return false;" style="color:'.$co.'">' . ($i+1) . '</a>]</span>&nbsp;&nbsp;';
                        $sta1+=$s;
                        $stp1=$sta1+$s;
                    }
                ?>
        </div>
    </div>
</div>
