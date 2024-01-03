<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
<script charset="utf-8" src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

$(document).ready(async function () {
    liff.init({ liffId: "2000369709-DxRo3W7Z" }, async () => {
        if (!liff.isLoggedIn()) {
            liff.login({ redirectUri: 'https://www.nctsc.space/gameroom/login.php?page=line' });
        } else {
            getProfile();
        }
    });
});


async function getProfile() {
    try {
        var profile = await liff.getProfile();
        var userId = profile.userId;
        var pictureUrl = profile.pictureUrl;
        var displayName = profile.displayName;
        var email = await liff.getDecodedIDToken().email;

        var formdata = new FormData();
        formdata.append("mId", userId);
        formdata.append("pictureUrl", pictureUrl);
        formdata.append("displayName", displayName);
        formdata.append("email", email);

        $.ajax({
        url:"liff.register.php",
        type:"POST",
        data:formdata,
        dataType:"json",
        contentType:false,
        processData:false,
        success:function(data){
            if(data == 1){
                Swal.fire({
                    position:"top-end",
                    icon:"success",
                    title:"login success",
                    showConfirmButton:false,
                    timer:900
                }).then((result) => {
                    window.location.href = "index2.php";
                });
            }else{
                Swal.fire({
                    position:"top-end",
                    icon:"error",
                    title:"login fail",
                    showConfirmButton:false,
                    timer:900
                }).then((result) => {
                    window.location.href = "login.php?page=gameRoom";
                });
            }
        }
    });
    } catch (error) {
        console.error("เกิดข้อผิดพลาด: " + error);
    }
}


  
</script>