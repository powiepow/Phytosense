<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .forgot-password-container {
            background: #ffffff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .forgot-password-container h1 {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 1rem;
        }

        .forgot-password-container p {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 1.5rem;
        }

        .forgot-password-container input[type="email"] {
            width: 100%;
            padding: 0.8rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }

        .forgot-password-container button {
            width: 100%;
            padding: 0.8rem;
            background-color: #5e8e56;
            color: #fff;
            font-size: 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .forgot-password-container button:hover {
            background-color: #4a7244;
        }

        .forgot-password-container a {
            display: inline-block;
            margin-top: 1rem;
            color: #5e8e56;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .forgot-password-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="forgot-password-container">
        <h1>Forgot Password?</h1>
        <p>Enter your email address below and we will send you instructions to reset your password.</p>
        <form action="/reset_email" method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit">Send Reset Link</button>
        </form>
        <a href="/signin">Back to Login</a>
    </div>
</body>
</html>
