<?php
/*
Plugin Name: NYX-EI Maintenance
Description: Active un mode maintenance avec une page personnalisée.
Version: 1.2
Author: NYX-EI
*/

function nyx_ei_maintenance_mode() {
    if (!current_user_can('manage_options') && !is_user_logged_in()) {
        header($_SERVER["SERVER_PROTOCOL"] . " 503 Service Unavailable");
        header("Retry-After: 3600");
        
        $end_time = time() + (3 * 24 * 60 * 60); // 3 jours
        
        echo '<!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Nous revenons bientôt - NYX-EI</title>
            <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
            <script>
                function updateCountdown() {
                    const endTime = ' . $end_time . ' * 1000;
                    const interval = setInterval(() => {
                        const now = new Date().getTime();
                        const distance = endTime - now;
                        if (distance < 0) {
                            clearInterval(interval);
                            document.getElementById("countdown").innerHTML = "Maintenance terminée";
                            return;
                        }
                        const days = String(Math.floor(distance / (1000 * 60 * 60 * 24))).padStart(2, '0');
                        const hours = String(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
                        const minutes = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                        const seconds = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');
                        document.getElementById("countdown").innerHTML = `${days} : ${hours} : ${minutes} : ${seconds}`;
                    }, 1000);
                }
            </script>
            <style>
                body {
                    background: url("' . plugins_url('background.jpg', __FILE__) . '") no-repeat center center fixed;
                    background-size: cover;
                    color: #ffffff;
                    font-family: "Montserrat", sans-serif;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                    text-align: center;
                }
                .container {
                    max-width: 600px;
                    padding: 40px;
                    background-color: rgba(0, 0, 0, 0.9);
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
                }
                .logo {
                    max-width: 100px;
                    margin-bottom: 20px;
                }
                h1 {
                    font-size: 2.5em;
                    font-weight: 700;
                    margin-bottom: 20px;
                }
                p {
                    font-size: 1.2em;
                    font-weight: 300;
                    margin-bottom: 20px;
                }
                .contact {
                    font-size: 1em;
                    font-weight: 300;
                    margin-top: 20px;
                }
                .countdown {
                    font-size: 2em;
                    font-weight: bold;
                    margin-top: 20px;
                    letter-spacing: 3px;
                }
                .email {
                    margin-top: 20px;
                    font-size: 1em;
                    font-weight: 300;
                }
            </style>
        </head>
        <body onload="updateCountdown()">
            <div class="container">
                <img src="' . plugins_url('logo.png', __FILE__) . '" alt="NYX-EI Logo" class="logo">
                <h1>We Are Coming Soon</h1>
                <div id="countdown" class="countdown">00 : 00 : 00 : 00</div>
                <p>Nous croyons fermement que les meilleures solutions viennent de nouvelles perspectives et de repousser les limites conventionnelles.</p>
                <div class="contact">
                    <p>B.P 17623 Yaoundé</p>
                    <p>+237 697 99 15 90</p>
                </div>
                <div class="email">Say hello! contact@nyx-ei.tech</div>
            </div>
        </body>
        </html>';
        exit;
    }
}
add_action('init', 'nyx_ei_maintenance_mode');
