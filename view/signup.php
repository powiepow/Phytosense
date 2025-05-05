<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Phytosense</title>
</head>
<body>
    <main class="sgn-container">
        <form id="signupForm" class="sgn-card hidden" method="POST">
            <span><a class="sgn-back" href="javascript:window.history.back();">Back</a></span>
            <div class="upper-txt">
                <img class="sgn-logo" src="assets/images/logo.png" alt="">
                <p>Sign Up</p>
                <img class="sgn-br" src="assets/images/20.png" alt="">
            </div>
            <small style="color:red;" id="responseMessage"></small>
            <p></p>
            <input required name="fname" type="text" placeholder="Firstname">
            <input required name="lname" type="text" placeholder="Lastname">
            <input name="username" required type="text" placeholder="Username">
            <input required name="email" type="email" placeholder="Email">
            <input name="password" required type="password" placeholder="Password">
            <small></small>
            <div class="sgn-type">
                <span class="type-container">
                    <p>Farmer</p>
                    <input id="radio1" name="userType" value="Farmer" type="radio" onclick="deselectOther('radio2')">
                </span>
                <span class="type-container">
                    <p>Non-Farmer</p>
                    <input id="radio2" checked name="userType" value="Individual" type="radio" onclick="deselectOther('radio1')">
                </span>    
            </div>
            <button name="signup">Sign Up</button>
            
        </form>
    </main>
</body>
<script src="assets/js/style.js"></script>
<script src="assets/js/function.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        signup();
    });
    function deselectOther(id) {
      document.getElementById(id).checked = false;
    }
  </script>
  
</html>