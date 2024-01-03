<?php

$channelAccessToken = 'p3ONadgsCiWssE911UfTuGZofbJOGwPasxQ/b6lnHKVJT2/H5pmKDpIgs9yutXV7efrvwamW2voSyTPVfXB3IIg7zXMTajZ3VpcWfTdXJy17g6yHjgAk8boLhyZVNj9YSANcpkk99DVa51BzOiNeMgdB04t89/1O/w1cDnyilFU=';

if($_GET['id'] == 1){
$endpoint = 'https://api.line.me/v2/bot/richmenu';
$data = [
    "size"=> [
      "width"=> 2500,
      "height"=> 843
    ],
    "selected"=> true,
    "name"=> "Rich Menu 2",
    "chatBarText"=> "Bulletin",
    "areas"=> [
        [
          "bounds"=> [
            "x"=> 76,
            "y" => 55,
            "width"=> 625,
            "height"=> 350
        ],
          "action"=> [
            "type"=> "message",
            "text"=> "User"
        ]
        ]
      ]
    ];
$jsonData = json_encode($data);
$headers = [
    'Authorization: Bearer ' . $channelAccessToken,
    'Content-Type: application/json',
];
$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
$response = curl_exec($ch);
if ($response === false) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    echo 'Response: ' . $response;
}
curl_close($ch);
}
 







if($_GET['id'] == 2){
$richMenuId = 'richmenu-b00ce5af909d032b7dd86d620596a635';
$endpoint = "https://api-data.line.me/v2/bot/richmenu/{$richMenuId}/content";
$imageType = 'image/jpeg'; // หรือ 'image/png' ตามความเหมาะสม
$imageFile = 'img/user.jpg'; // แทนที่ด้วยเส้นทางไปยังไฟล์รูปภาพที่คุณต้องการอัปโหลด
$headers = [
    'Authorization: Bearer ' . $channelAccessToken,
    'Content-Type: ' . $imageType,
];
$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($imageFile));
$response = curl_exec($ch);
if ($response === false) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    echo 'Response: ' . $response;
}
curl_close($ch);



}

if($_GET['id'] == 3){
    $richMenuId = 'richmenu-b00ce5af909d032b7dd86d620596a635'; // แทนที่ด้วย Rich Menu ID ที่คุณต้องการใช้

$apiEndpoint = 'https://api.line.me/v2/bot/user/all/richmenu/' . $richMenuId;

$headers = array(
    'Authorization: Bearer ' . $channelAccessToken,
);

$ch = curl_init($apiEndpoint);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
}

curl_close($ch);

// ตรวจสอบการตอบกลับจาก API และดำเนินการต่อตามความเหมาะสม
if ($response) {
    // คำขอสำเร็จ
    echo 'Rich Menu ถูกกำหนดให้กับผู้ใช้ทั้งหมดแล้ว'.$response;
} else {
    // คำขอไม่สำเร็จ
    echo 'มีข้อผิดพลาดเกิดขึ้นในการกำหนด Rich Menu';
}
}
?>
