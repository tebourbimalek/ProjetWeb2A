<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spotify - Modifier les méthodes de connexion</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Circular', 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }
        
        body {
            background-color: #121212;
            color: white;
            min-height: 100vh;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .logo {
            display: flex;
            align-items: center;
        }
        
        .logo img {
            height: 35px;
        }
        
        .header-nav {
            display: flex;
            gap: 24px;
        }
        
        .header-nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
        }
        
        .profile {
            display: flex;
            align-items: center;
            gap: 8px;
            background-color: #000000;
            border-radius: 20px;
            padding: 6px 12px;
            cursor: pointer;
        }
        
        .profile img {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: #535353;
        }
        
        .search-bar {
            margin: 24px auto;
            max-width: 700px;
        }
        
        .search-bar input {
            width: 100%;
            padding: 14px 40px;
            border-radius: 4px;
            border: none;
            background-color: #242424;
            color: white;
            font-size: 16px;
        }
        
        .search-bar input::placeholder {
            color: #aaa;
        }
        
        .back-button {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px 0;
            cursor: pointer;
        }
        
        .content {
            max-width: 700px;
            margin: 20px auto;
        }
        
        h1 {
            font-size: 32px;
            margin-bottom: 12px;
        }
        
        .subtitle {
            color: #b3b3b3;
            margin-bottom: 32px;
            line-height: 1.5;
        }
        
        .section-title {
            font-size: 18px;
            margin: 24px 0 16px;
        }
        
        .login-method {
            background-color: #242424;
            border-radius: 8px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            cursor: pointer;
        }
        
        .login-method.active {
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .method-icon {
            width: 24px;
            height: 24px;
            margin-right: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .method-name {
            flex-grow: 1;
        }
        
        .login-info {
            font-size: 14px;
            color: #b3b3b3;
            padding: 16px 20px;
            line-height: 1.5;
        }
        
        .button {
            background-color: #1DB954;
            color: black;
            border: none;
            border-radius: 32px;
            padding: 12px 32px;
            font-weight: bold;
            cursor: pointer;
            font-size: 14px;
            margin-top: 8px;
            margin-left: auto;
            display: block;
        }
        
        footer {
            margin-top: 80px;
            padding: 40px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
            gap: 24px;
        }
        
        .footer-logo img {
            height: 40px;
            margin-bottom: 40px;
        }
        
        .footer-column h3 {
            color: #b3b3b3;
            font-size: 14px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .footer-column ul {
            list-style: none;
        }
        
        .footer-column ul li {
            margin-bottom: 16px;
        }
        
        .footer-column a {
            color: white;
            text-decoration: none;
            font-size: 16px;
        }
        
        .footer-column a:hover {
            text-decoration: underline;
        }
        
        .social-links {
            display: flex;
            gap: 16px;
            margin-top: 16px;
        }
        
        .social-icon {
            width: 44px;
            height: 44px;
            background-color: #292929;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <svg viewBox="0 0 63 20" height="35" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMin meet">
                    <g fill-rule="evenodd" fill="currentColor">
                        <path d="M61.842 9.506a1.02 1.02 0 0 1-1.023-1.024c0-.562.453-1.03 1.029-1.03a1.02 1.02 0 0 1 1.023 1.024 1.03 1.03 0 0 1-1.029 1.03m.006-1.952a.915.915 0 0 0-.922.928c0 .51.394.921.916.921a.916.916 0 0 0 .922-.927.908.908 0 0 0-.916-.922m.226 1.027l.29.406h-.244l-.26-.372h-.225v.372h-.204V7.912h.48c.249 0 .413.128.413.343 0 .176-.102.284-.25.326m-.172-.485h-.267v.34h.267c.133 0 .212-.065.212-.17 0-.11-.08-.17-.212-.17m-12.804-3.52a1.043 1.043 0 1 0-.001 2.086 1.043 1.043 0 0 0 0-2.087m.72 2.89h-1.454a.107.107 0 0 0-.106.107v6.346c0 .06.047.107.106.107h1.455a.107.107 0 0 0 .107-.107V7.572a.107.107 0 0 0-.107-.107m3.233.006v.006h-.006V7.479a.106.106 0 0 0-.106.107v11.277c0 .06.047.107.106.107h1.455a.107.107 0 0 0 .107-.107V8.586c0-.356.273-.629.63-.629h.765a.106.106 0 0 0 .106-.107V6.556a.107.107 0 0 0-.106-.107h-.766c-1.086 0-2.019.648-2.147 1.516-.018.13-.129.123-.132-.009zm15.612-.86c-1.624-.388-3.197 1.11-3.197 2.451v8.673c0 .06.047.107.106.107h1.455a.107.107 0 0 0 .106-.107V9.278c0-.834.498-1.585 1.31-1.78.196-.04.107-.235-.106-.196zm-5.506 4.78c.023-.163.015-.417.168-.595.547-.655 1.583-1.106 2.543-.906 1.953.413 2.151 2.692 2.152 3.925.001 1.397-.346 3.903-2.439 3.903-1.035 0-1.925-.503-2.314-1.423-.069-.162-.088-.428-.11-.695v-4.21zm-.166-2.677c0-.06-.047-.107-.106-.107h-1.455a.107.107 0 0 0-.106.107v11.277c0 .06.017.318.08.335.008.002.568.002 1.534 0 .06 0 .052-2.14.052-2.36v-.574c.505 1.092 1.647 1.502 2.823 1.502 2.681 0 3.954-2.401 3.954-5.158 0-3.177-1.703-5.119-4.383-4.873-1.218.077-2.246.643-2.757 1.85-.038.075-.053-.118-.053-.224V7.573zM28.463 4.866c-.007-.01-.016-.018-.027-.026l-3.248-2.752a.173.173 0 0 0-.252.025l-1.949 2.327a.166.166 0 0 0 .022.241l3.247 2.751c.01.008.018.017.026.026.193.185.464.22.68.077.216-.144.303-.407.198-.641a.5.5 0 0 0-.078-.121 1.166 1.166 0 0 1-.628-1.595 1.166 1.166 0 0 1 1.06-.656c.366 0 .725.18.942.509a.217.217 0 0 0 .032.041.497.497 0 0 0 .581.094.498.498 0 0 0 .221-.59.515.515 0 0 0-.023-.061 1.36 1.36 0 0 1-.059-1.13c.13-.342.396-.612.732-.75a1.398 1.398 0 0 1 1.438.276c.27.258.424.612.424.984 0 .487-.264.954-.684 1.175-.213.087-.303.385-.112.631a.496.496 0 0 0 .436.24.473.473 0 0 0 .152 0c.436-.067 1.04-.38 1.471-.933.305-.392.489-.874.534-1.367a2.637 2.637 0 0 0-1.63-2.772 2.617 2.617 0 0 0-2.835.489 2.625 2.625 0 0 0-2.918-.46 2.625 2.625 0 0 0-1.498 2.654c.07.654.327 1.258.75 1.73M13.89 19.054c-2.679 0-5.225-1.092-7.195-3.034-.43-.422-1.115-.418-1.534.01a1.09 1.09 0 0 0 .013 1.534c2.39 2.358 5.526 3.645 8.717 3.645 6.93.001 12.571-5.698 12.571-12.662 0-3.292-1.293-6.385-3.646-8.717a1.086 1.086 0 0 0-1.535 0 1.092 1.092 0 0 0 .001 1.535 10.517 10.517 0 0 1 3.034 7.182c0 5.745-4.679 10.478-10.427 10.507M2.573 10.387c0-3.157 1.219-6.085 3.435-8.272a1.092 1.092 0 0 0 .01-1.534 1.084 1.084 0 0 0-1.534-.01C1.997 3.025.418 6.602.418 10.387c0 2.533.658 4.927 1.884 7.018.267.457.798.603 1.254.342.456-.261.61-.81.343-1.268a10.458 10.458 0 0 1-1.326-5.092"></path>
                    </g>
                </svg>
            </div>
            <div class="header-nav">
                <a href="#">Premium</a>
                <a href="#">Assistance</a>
                <a href="#">Télécharger</a>
            </div>
            <div class="profile">
                <div class="profile-icon">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 18 20">
                        <path d="M15.216 13.717L12 11.869C11.823 11.768 11.772 11.607 11.772 11.438C11.924 10.974 12.028 10.472 12.028 9.948C12.028 8.872 11.542 7.898 10.719 7.219C9.896 6.539 8.672 6.199 7.497 6.208C6.322 6.218 5.12 6.578 4.307 7.259C3.493 7.937 3.016 8.913 3.016 9.989C3.016 10.513 3.12 11.015 3.272 11.478C3.272 11.647 3.221 11.808 3.044 11.909L0 13.898V16.165H1.327V14.306L3.728 12.788L3.432 12.292C3.212 11.889 3.06 11.437 3.06 10.939C3.06 10.91 3.057 8.554 5.531 8.433C7.814 8.401 9.024 10.751 9.042 10.94C9.042 11.437 8.89 11.889 8.669 12.292L8.373 12.788L10.774 14.306V16.165H16.546V13.717H15.216Z"></path>
                    </svg>
                </div>
                <span>Profil</span>
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M14 6l-6 6-6-6h12z"></path>
                </svg>
            </div>
        </header>
        
        <div class="search-bar">
            <input type="text" placeholder="Rechercher un compte ou des articles d'aide">
        </div>
        
        <div class="content">
            <div class="back-button">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M15.54 21.15L5.095 12.23 15.54 3.31l.65.76-9.555 8.16 9.555 8.16-.65.76z"></path>
                </svg>
            </div>
            
            <h1>Modifier les méthodes de connexion</h1>
            <p class="subtitle">Ajoutez une ou plusieurs méthodes de connexion pour vous assurer de pouvoir accéder à votre compte à tout moment.</p>
            
            <h2 class="section-title">Méthodes de connexion actuelles</h2>
            <div class="login-method active">
                <div class="method-icon">
                    <svg width="18" height="18" viewBox="0 0 18 18">
                        <path d="M9 3.48c1.69 0 2.83.73 3.48 1.34l2.54-2.48C13.46.89 11.43 0 9 0 5.48 0 2.44 2.02.96 4.96l2.91 2.26C4.6 5.05 6.62 3.48 9 3.48z" fill="#EA4335"></path>
                        <path d="M17.64 9.2c0-.74-.06-1.28-.19-1.84H9v3.34h4.96c-.1.83-.64 2.08-1.84 2.92l2.84 2.2c1.7-1.57 2.68-3.88 2.68-6.62z" fill="#4285F4"></path>
                        <path d="M3.88 10.78A5.54 5.54 0 0 1 3.58 9c0-.62.11-1.22.29-1.78L.96 4.96A9.008 9.008 0 0 0 0 9c0 1.45.35 2.82.96 4.04l2.92-2.26z" fill="#FBBC05"></path>
                        <path d="M9 18c2.43 0 4.47-.8 5.96-2.18l-2.84-2.2c-.76.53-1.78.9-3.12.9-2.38 0-4.4-1.57-5.12-3.74L.97 13.04C2.45 15.98 5.48 18 9 18z" fill="#34A853"></path>
                    </svg>
                </div>
                <div class="method-name">Google</div>
            </div>
            
            <h2 class="section-title">Méthodes de connexion disponibles</h2>
            <div class="login-method">
                <div class="method-icon">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 11c-.55 0-1-.45-1-1V8c0-.55.45-1 1-1s1 .45 1 1v4c0 .55-.45 1-1 1zm1 4h-2v-2h2v2z"></path>
                    </svg>
                </div>
                <div class="method-name">Connectez-vous pour continuer</div>
            </div>
            
            <div class="login-info">
                Pour ajouter une nouvelle méthode de connexion à votre compte en toute sécurité, nous devons valider votre identité. Veuillez vous reconnecter à Spotify pour continuer.
                <button class="button">Connexion</button>
            </div>
            
            <div class="login-method">
                <div class="method-icon">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"></path>
                    </svg>
                </div>
                <div class="method-name">E-mail et mot de passe</div>
            </div>
            
            <div class="login-method">
                <div class="method-icon">
                    <svg width="24" height="24" fill="#3b5998" viewBox="0 0 24 24">
                        <path d="M22.675 0H1.325C.593 0 0 .593 0 1.325v21.351C0 23.407.593 24 1.325 24H12.82v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116c.73 0 1.323-.593 1.323-1.325V1.325C24 .593 23.407 0 22.675 0z"></path>
                    </svg>
                </div>
                <div class="method-name">Facebook</div>
            </div>
            
            <div class="login-method">
                <div class="method-icon">
                    <svg width="24" height="24" fill="#A2AAAD" viewBox="0 0 24 24">
                        <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.19 2.3-.89 3.45-.75 1.31.15 2.41.62 3.07 1.53-2.69 1.77-2.05 5.66.62 6.71-.51 1.63-1.47 3.27-2.22 3.68zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.26 2.01-1.62 4.19-3.74 4.25z"></path>
                    </svg>
                </div>
                <div class="method-name">Apple</div>
            </div>
        </div>
        
        <footer>
            <div class="footer-content">
                <div class="footer-logo">
                    <svg viewBox="0 0 63 20" height="40" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMin meet">
                        <g fill-rule="evenodd" fill="currentColor">
                            <path d="M61.842 9.506a1.02 1.02 0 0 1-1.023-1.024c0-.562.453-1.03 1.029-1.03a1.02 1.02 0 0 1 1.023 1.024 1.03 1.03 0 0 1-1.029 1.03m.006-1.952a.915.915 0 0 0-.922.928c0 .51.394.921.916.921a.916.916 0 0 0 .922-.927.908.908 0 0 0-.916-.922m.226 1.027l.29.406h-.244l-.26-.372h-.225v.372h-.204V7.912h.48c.249 0 .413.128.413.343 0 .176-.102.284-.25.326m-.172-.485h-.267v.34h.267c.133 0 .212-.065.212-.17 0-.11-.08-.17-.212-.17m-12.804-3.52a1.043 1.043 0 1 0-.001 2.086 1.043 1.043 0 0 0 0-2.087m.72 2.89h-1.454a.107.107 0 0 0-.106.107v6.346c0 .06.047.107.106.107h1.455a.107.107 0 0 0 .107-.107V7.572a.107.107 0 0 0-.107-.107m3.233.006v.006h-.006V7.479a.106.106 0 0 0-.106.107v11.277c0 .06.047.107.106.107h1.455a.107.107 0 0 0 .107-.107V8.586c0-.356.273-.629.63-.629h.765a.106.106 0 0 0 .106-.107V6.556a.107.107 0 0 0-.106-.107h-.766c-1.086 0-2.019.648-2.147 1.516-.018.13-.129.123-.132-.009zm15.612-.86c-1.624-.388-3.197 1.11-3.197 2.451v8.673c0 .06.047.107.106.107h1.455a.107.107 0 0 0 .106-.107V9.278c0-.834.498-1.585 1.31-1.78.196-.04.107-.235-.106-.196zm-5.506 4.78c.023-.163.015-.417.168-.595.547-.655 1.583-1.106 2.543-.906 1.953.413 2.151 2.692 2.152 3.925.001 1.397-.346 3.903-2.439 3.903-1.035 0-1.925-.503-2.314-1.423-.069-.162-.088-.428-.11-.695v-4.21zm-.166-2.677c0-.06-.047-.107-.106-.107h-1.455a.107.107 0 0 0-.106.107v11.277c0 .06.017.318.08.335.008.002.568.002 1.534 0 .06 0 .052-2.14.052-2.36v-.574c.505 1.092 1.647 1.502 2.823 1.502 2.681 0 3.954-2.401 3.954-5.158 0-3.177-1.703-5.119-4.383-4.873-1.218.077-2.246.643-2.757 1.85-.038.075-.053-.118-.053-.224V7.573zM28.463 4.866c-.007-.01-.016-.018-.027-.026l-3.248-2.752a.173.173 0 0 0-.252.025l-1.949 2.327a.166.166 0 0 0 .022.241l3.247 2.751c.01.008.018.017.026.026.193.185.464.22.68.077.216-.144.303-.407.198-.641a.5.5 0 0 0-.078-.121 1.166 1.166 0 0 1-.628-1.595 1.166 1.166 0 0 1 1.06-.656c.366 0 .725.18.942.509a.217.217 0 0 0 .032.041.497.497 0 0 0 .581.094.498.498 0 0 0 .221-.59.515.515 0 0 0-.023-.061 1.36 1.36 0 0 1-.059-1.13c.13-.342.396-.612.732-.75a1.398 1.398 0 0 1 1.438.276c.27.258.424.612.424.984 0 .487-.264.954-.684 1.175-.213.087-.303.385-.112.631a.496.496 0 0 0 .436.24.473.473 0 0 0 .152 0c.436-.067 1.04-.38 1.471-.933.305-.392.489-.874.534-1.367a2.637 2.637 0 0 0-1.63-2.772 2.617 2.617 0 0 0-2.835.489 2.625 2.625 0 0 0-2.918-.46 2.625 2.625 0 0 0-1.498 2.654c.07.654.327 1.258.75 1.73M13.89 19.054c-2.679 0-5.225-1.092-7.195-3.034-.43-.422-1.115-.418-1.534.01a1.09 1.09 0 0 0 .013 1.534c2.39 2.358 5.526 3.645 8.717 3.645 6.93.001 12.571-5.698 12.571-12.662 0-3.292-1.293-6.385-3.646-8.717a1.086 1.086 0 0 0-1.535 0 1.092 1.092 0 0 0 .001 1.535 10.517 10.517 0 0 1 3.034 7.182c0 5.745-4.679 10.478-10.427 10.507M2.573 10.387c0-3.157 1.219-6.085 3.435-8.272a1.092 1.092 0 0 0 .01-1.534 1.084 1.084 0 0 0-1.534-.01C1.997 3.025.418 6.602.418 10.387c0 2.533.658 4.927 1.884 7.018.267.457.798.603 1.254.342.456-.261.61-.81.343-1.268a10.