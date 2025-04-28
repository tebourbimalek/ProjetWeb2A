<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SongQuiz Menu</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background-color: #121212;
      color: white;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
    }

    header {
      background-color: #1f1f1f;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 12px 32px;
      border-bottom: 4px solid #333;
      flex-direction: row-reverse; /* This switches order */
    }

    .logo {
      height: 50px;
    }

    .header-buttons {
      display: flex;
      gap: 16px;
    }

    .header-buttons button {
      background-color: #9c27b0;
      border: none;
      color: white;
      padding: 10px 16px;
      font-weight: bold;
      border-radius: 6px;
      cursor: pointer;
    }

    .header-buttons button:hover {
      background-color: #ba68c8;
    }

    h1 {
      text-align: center;
      margin: 30px 0 10px;
      font-size: 2rem;
    }

    .container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
      width: 100%;
      max-width: 1000px;
      padding: 30px;
      margin: auto;
    }

    .card {
      background-color: #1e1e2e;
      border-radius: 12px;
      padding: 2rem;
      text-align: center;
      box-shadow: 0 4px 10px rgba(0,0,0,0.4);
      transition: transform 0.2s ease;
      cursor: pointer;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .card i {
      font-size: 2rem;
      margin-bottom: 1rem;
      color: #ffffffcc;
    }

    .card h2 {
      font-size: 1.3rem;
      margin-bottom: 0.5rem;
    }

    .card p {
      color: #cccccc;
      font-size: 0.95rem;
    }
  </style>
</head>
<body>

<header>
  <div class="header-buttons">
    <button>Se connecter</button>
    <button>Premium</button>
  </div>
  <img src="assets/img/logoprojet1.jpg" alt="Tunify Logo" class="logo">
</header>

<h1>SongQuiz Menu</h1>

<div class="container">
  <div class="card">
    <i class="fas fa-play-circle"></i>
    <h2>Quickplay</h2>
    <p>Jump into a quick game and test your song knowledge!</p>
  </div>

  <div class="card">
    <i class="fas fa-music"></i>
    <h2>Heardle</h2>
    <p>Guess the song by listening to the first few seconds!</p>
  </div>

  <div class="card" onclick="window.location.href='arcade.php'">
  <i class="fas fa-gamepad"></i>
  <h2>Arcade</h2>
  <p>Hands-free music quizzes with no need to interact!</p>
</div>


  <div class="card">
    <i class="fas fa-newspaper"></i>
    <h2>Updates</h2>
    <p>Check out the latest changes from the team!</p>
  </div>
</div>

</body>
</html>
