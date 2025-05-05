
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/style/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Phytosense</title>
</head>
<body>
    <main class="sgn-container">
        <form id="signinForm" method="POST" class="sgn-card hidden">
            <span><a class="sgn-back" href="javascript:window.history.back();">Back</a></span>
            <div class="upper-txt">
                <img class="sgn-logo" src="../../assets/images/logo.png" alt="">
                <p>Sign In</p>
                <img class="sgn-br" src="../../assets/images/20.png" alt="">
            </div>
            <small style="color:red;" id="responseMessage"></small>
            <input id="username" type="text" required placeholder="Username">
            <input id="password" type="password" required placeholder="Password">
            <button name="signin">Sign In</button>
        </form>
    </main>
</body>
<script src="../../assets/js/style.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="../../assets/js/function2.js"></script>
<script>
ad_b_signin();
   
</script>
</html>