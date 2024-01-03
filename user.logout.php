
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<script src="https://static.line-scdn.net/liff/edge/2.1/sdk.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
<script>
    $(document).ready( function () {
    liff.init({ liffId: "2000369709-DxRo3W7Z" }.then( () => {
        liff.logout();
    }) 
    )
});
</script>
</body>
</html>
<?php
    session_start();
    session_destroy();
    echo "1";
?>



