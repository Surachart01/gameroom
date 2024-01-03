<?php
if(!isset($_POST['email']))
{
	echo "<script>window.location='https://www.nctsc.com';</script>";
	exit();	
}
require 'phpmailer/PHPMailer.php';// path to the PHPMailer class.
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Esception;



$fm = "nctsdirewolf001@gmail.com"; // *** ต้องใช้อีเมล์ @gmail.com เท่านั้น ***
$to = "j.yoottana@gmail.com"; // อีเมล์ที่ใช้รับข้อมูลจากแบบฟอร์ม
$custemail = $_POST['email']; // อีเมล์ของผู้ติดต่อที่กรอกผ่านแบบฟอร์ม
 
$subj = "หัวข้อ: มีข้อความเข้าค่ะจาก " . $_POST["phone"] ;
 
/* ------------------------------------------------------------------------------------------------------------- */
$message.= "ชื่อ-นามสกุล: ".$_POST['name']."\n";
$message.= "อีเมล์: ".$custemail."\n";
$message.= "หัวข้อ: มีข้อความเข้าค่ะ \n";
$message.= "รายละเอียด: ".$_POST['details']."\n";
/* ------------------------------------------------------------------------------------------------------------- */
 
/* หากต้องการรับข้อมูลจาก Form แบบไม่ระบุชื่อตัวแปร สามารถรับข้อมูลได้ไม่จำกัด ให้ลบบรรทัด 11-14 แล้วใช้ 19-22 แทน
     
foreach ($_POST as $key => $value) {
 //echo "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";
 $message.= "Field ".htmlspecialchars($key)." = ".htmlspecialchars($value)."\n";
}
 
*/
 
$mesg = $message;
$mail = new PHPMailer();
$mail->CharSet = 'utf-8'; 
 
/* ------------------------------------------------------------------------------------------------------------- */
/* ตั้งค่าการส่งอีเมล์ โดยใช้ SMTP ของ Gmail */
$mail->IsSMTP();
$mail->Mailer = 'smtp';
$mail->IsSMTP(true); // telling the class to use SMTP
$mail->SMTPAuth = true;                  // enable SMTP authentication
$mail->SMTPSecure = 'tls';                 // sets the prefix to the servier
$mail->isHTML(true);
$mail->setFrom($fm);
$mail->Host = 'smtp.gmail.com';      // sets GMAIL as the SMTP server
$mail->Port = 587;                   // set the SMTP port for the GMAIL server
$mail->Username = 'nctsdirewolf001@gmail.com';  // Gmail Email username
$mail->Password = 'gxkijrmopolqhrir';            // App Password not Gmail password
/* ------------------------------------------------------------------------------------------------------------- */
 
//$mail->From = $fm;
//$mail->AddAddress($to);
//$mail->AddReplyTo($custemail);
$mail->addAddress($to,'YOOTTANA');
$mail->Subject = $subj;
$mail->Body     = $mesg;
$mail->WordWrap = 50;  
//
try
{
	if(!$mail->send()) {
	
	echo '<script>alert("เกิดข้อผิดพลาด คุณสามารถติดต่อทาง LINE ได้เลยค่ะ");window.location="index.html";</script>';
	exit();
	} else {
	echo '<script>alert("ส่งเมลล์เรียบร้อยค่ะ ผู้ดูแลระบบจะทำการติดต่อกลับเร็วที่สุด ขอบคุณค่ะ");window.location="index.html";</script>';
	exit();
	}
} catch (phpmailerException $e) {
  $txt =  $e->errorMessage(); //Pretty error messages from PHPMailer
  echo '<script>alert(' . $txt .');window.location="index.html";</script>';
	exit();
	
} catch (Exception $e) {
	$txt='error';
  echo '<script>alert(' . $txt .');window.location="index.html";</script>';
}
?>