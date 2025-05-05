<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Code Confirmation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f5f5f5;
        }

        .container {
            background: #ffffff;
            width: 100%;
            max-width: 400px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .container h1 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }

        .container p {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }

        .container form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .container input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .container button {
            padding: 10px;
            font-size: 16px;
            color: #ffffff;
            background-color: #5e8e56;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .container button:hover {
            background-color: #4a6e45;
        }

        .container .back-link {
            display: block;
            margin-top: 15px;
            font-size: 14px;
            color: #5e8e56;
            text-decoration: none;
        }

        .container .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Enter New Password</h1>
        <p></p>
        <form action="/reset_email" method="POST">
            <input type="text" name="new_pass" required placeholder="Enter New Password" required>
            <input type="text" name="reset_code" hidden value="<?php if(isset($_GET['token'])){echo $_GET['token'];}else{echo "empty";}?>">
            <button type="submit">Submit</button>
        </form>
        <a href="/signin" class="back-link">Back to Signin</a>
    </div>
</body>
</html>
