<?php
/*
Plugin Name: NYX-EI Maintenance Mode
Description: Active un mode maintenance avec une page personnalisée.
Version: 1.1
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
            <title>Site en Maintenance - NYX-EI</title>
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
                        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                        document.getElementById("countdown").innerHTML = `${days}j ${hours}h ${minutes}m ${seconds}s`;
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
                    padding: 20px;
                    background-color: rgba(0, 0, 0, 0.8);
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
                }
                .logo {
                    max-width: 150px;
                    margin-bottom: 20px;
                }
                h1 {
                    font-size: 2.5em;
                    margin-bottom: 20px;
                }
                p {
                    font-size: 1.2em;
                    margin-bottom: 20px;
                }
                .contact {
                    font-size: 1em;
                    margin-top: 20px;
                }
                .countdown {
                    font-size: 1.5em;
                    font-weight: bold;
                    margin-top: 20px;
                }
            </style>
        </head>
        <body onload="updateCountdown()">
            <div class="container">
                <img src="' . plugins_url('logo.png', __FILE__) . '" alt="NYX-EI Logo" class="logo">
                <h1>Site en Maintenance</h1>
                <p>Nous effectuons actuellement des travaux de maintenance. Nous serons de retour très bientôt.</p>
                <div id="countdown" class="countdown"></div>
                <div class="contact">
                    <p>B.P 17623 Yaoundé</p>
                    <p>+237 697 99 15 90</p>
                    <p>contact@nyx-ei.tech</p>
                </div>
            </div>
        </body>
        </html>';
        exit;
    }
}
add_action('init', 'nyx_ei_maintenance_mode');
