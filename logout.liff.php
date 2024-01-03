<!DOCTYPE html>
<html>
<head>
    <title>LIFF Logout</title>
</head>
<body>
    <h1>Logout from LINE</h1>
    <p>Click the button below to logout from LINE.</p>
    <button id="logoutButton">Logout</button>

    <script src="https://static.line-scdn.net/liff/edge/2.1/sdk.js"></script>

    <script>
        liff.init({ liffId: "2000369709-DxRo3W7Z" }).then(() => {
            document.getElementById('logoutButton').addEventListener('click', function() {
                liff.logout();
            });
        }).catch((error) => {
            console.error("LIFF initialization failed:", error);
        });
    </script>
</body>
</html>
