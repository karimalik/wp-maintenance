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
        
        echo '<!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Nous revenons bientôt - NYX-EI</title>
            <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
            <style>
                body {
                    background-color: #A6242F;
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
                .email {
                    margin-top: 20px;
                    font-size: 1em;
                    font-weight: 300;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <img src="' . plugins_url('logo.png', __FILE__) . '" alt="NYX-EI Logo" class="logo">
                <h1>We Are Coming Soon</h1>
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
