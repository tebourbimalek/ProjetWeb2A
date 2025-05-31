<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spotify for Artists - Registration</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Circular', Helvetica, Arial, sans-serif;
        }
        
        body {
            background-color: #000;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        header {
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
        }
        
        .logo img {
            height: 35px;
        }
        
        .login-link {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
        }
        
        .login-link:hover {
            text-decoration: underline;
        }
        
        main {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0 20px;
            text-align: center;
        }
        
        h1 {
            font-size: 48px;
            margin-bottom: 20px;
            font-weight: 700;
        }
        
        .subtitle {
            font-size: 18px;
            margin-bottom: 40px;
        }
        
        .options-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            max-width: 400px;
            width: 100%;
            margin: 0 auto;
        }
        
        .option-card {
            flex: 1;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            transition: border-color 0.3s;
            text-decoration: none;
            color: #fff;
            background-color: rgba(30, 30, 30, 0.7);
        }
        
        .option-card:hover {
            border-color: #1DB954;
        }
        
        .option-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .circle-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            margin-bottom: 10px;
        }
        
        .circle-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .help-links {
            margin-top: 40px;
            text-align: center;
        }
        
        .help-links a {
            color: #fff;
            text-decoration: none;
            margin: 0 10px;
            font-size: 14px;
        }
        
        .help-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="\projetweb\assets\img\logo.png" alt="Tunify for Artists">
        </div>
        <a href="#" class="login-link">Already part of a team? Log in</a>
    </header>

    <main>
        <h1>Get access to Tunify for Artists</h1>
        <p class="subtitle">First, tell us who you are.</p>
        
        <div class="options-container">
            <a href="../tunisfy_sans_conexion/inscriptionAR.php?type=artist" class="option-card">
                <p class="option-title">Artist or manager</p>
                <div class="circle-image">
                    <img src="\projetweb\assets\img\artiste.jpg" alt="Artist or manager">
                </div>
            </a>
            
        </div>
        
        <div class="help-links">
            <a href="#">If your team already exists, ask an admin for access.</a>
            <br>
            <a href="#">Not sure which to choose?</a>
        </div>
    </main>
</body>
</html>