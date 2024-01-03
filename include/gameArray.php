<?php
$gameSta = array("0"=>"ดำเนินการ","1"=>"จบการเล่นทั้งหมด","2"=>"หยุดเกมส์ชั่วคราว","3"=>"สรุปเกมส์","4"=>"ยกเลิกเกมส์");
$roomCommand = array("USERJOIN"=>array("JOIN","JOIN FORCE","1"),
					"STARTPROBLEM"=>array("แจ้งปัญหา","แจ้งปัญหา","2"),
					"INSERTPROBLEM"=>array("เลือกปัญหา","insert problem","3"),
					"DELPROBLEM"=>array("DL","delete problem","4"),
					"PB1OK"=>array("PB1OK","step 1 is ok","5"),
					"TRACK"=>array("ติดตามปัญหา","Track My Problem","6"),
					"TEST"=>array("TT1","test","7"),
					"WORKOK"=>array("รับงาน","work ok","8"),
					"WORKNOTOK"=>array("ไม่รับงาน","work not ok","9"),
					"WORKNOTOKCF"=>array("ยืนยันการปฏิเสธงาน","work not ok confirm","10"),
					"WORKDETAIL"=>array("ดูรายละเอียดงาน","work detail","11"),
					"SHOWWORK"=>array("งานในระบบ","show work","12"),
					"SENDWORK"=>array("ส่งงาน","show work","13"),
					"SENDPIC"=>array("ส่งภาพ","send images","14"),
					"DELWORK"=>array("ล้างข้อมูล","del work","15"),
					"FINISHWORK"=>array("ส่งงานเสร็จสิ้น","finish work","16"),
					"ABOUTUS1"=>array("ข้อมูลทั่วไป","data1","17"),
					"ABOUTUS2"=>array("แจ้งข่าวสาร","data2","18"),
					"ABOUTUS3"=>array("บริการต่าง ๆ","data3","19"),
					"ABOUTUS4"=>array("เที่ยวกับเรา","data4","20"),
					"OLDWORK"=>array("งานที่เสร็จแล้ว","งานที่เคยทำมาแล้ว","21"),
					"PMWORK"=>array("แบบประเมิน","งานประเมินจากลูกบ้าน","22"),
					"MOREDATA"=>array("อ่านเพิ่มเติม","อ่านข้อมูลเพิ่มเติม","23")
				);// จะเปลี่ยนคำสั่งเปลี่ยนจากใน ARRAY ได้เลย 
				/* 
					"GEN"=>array("GEN","สร้างห้อง",3) โดย
					1. "GEN"=>array ถ้าเปลี่ยนตรงนี้ต้องเปลี่ยน command ข้างล่างด้วย
					2. "GEN" คือ คำสั่ง
					3. "สร้างห้อง" แปลคำสัั่งเป็นภาษาไทย
					4. 3 gameRoomStatus
				*/
$userCommandTD = array("B"=>array("ส","เสือ","BLUE"),
						"R"=>array("ม","มังกร","RED"),
						"O"=>array("P","คู่","ORANGE"),
						"G"=>array("T","tier","GREEN"),
						"M"=>array("C","Check My Money","MONEY"),
						"Z"=>array("Z","ZET OF STAT","ZET"),
						"USERDEL"=>array("DL","Del All","USERDEL")
					);
$B = $userCommandTD["B"][0];
$R = $userCommandTD["R"][0];
$O = $userCommandTD["O"][0];
$G = $userCommandTD["G"][0];

$BBg = '#0065A2';
$RBg = '#FF5768';
$OBg = '#FFBF65';
$GBg = '#00B0BA';


$BPrice = 1;
$RPrice = 1;
$OPrice = 12;
$GPrice = 31;




?>