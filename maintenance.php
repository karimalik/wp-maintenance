<?php
/*
Plugin Name: NYX-EI Maintenance
Description: Active un mode maintenance avec une page personnalisée.
Version: 1.0
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
            <title>Site en Maintenance - NYX-EI</title>
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
            </style>
        </head>
        <body>
            <div class="container">
                <img src="' . plugins_url('logo.png', __FILE__) . '" alt="NYX-EI Logo" class="logo">
                <h1>Site en Maintenance</h1>
                <p>Nous effectuons actuellement des travaux de maintenance. Nous serons de retour très bientôt.</p>
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
