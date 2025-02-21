<?php
/*
Plugin Name: NYX-EI Maintenance Mode
Description: Un plugin pour mettre le site en mode maintenance avec une page personnalisée.
Version: 1.0
Author: NYX-EI <help@nyx-ei.tech>
*/

function nyx_ei_maintenance_mode() {
    if (!current_user_can('edit_themes') || !is_user_logged_in()) {
        wp_die(
            '<!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Maintenance - NYX-EI</title>
                <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
                <style>
                    body {
                        background-color: #1a1a1a;
                        color: #ffffff;
                        font-family: "Montserrat", Arial, sans-serif;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        margin: 0;
                        text-align: center;
                    }
                    .maintenance-container {
                        max-width: 600px;
                        padding: 40px;
                        background-color: #2a2a2a;
                        border-radius: 10px;
                        box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
                    }
                    .logo {
                        max-width: 150px;
                        margin-bottom: 30px;
                    }
                    h1 {
                        font-size: 2.5em;
                        margin-bottom: 20px;
                        color: #A6242F;
                        font-weight: 700;
                    }
                    p {
                        font-size: 1.2em;
                        margin-bottom: 20px;
                        line-height: 1.6;
                    }
                    .contact-info {
                        font-size: 1em;
                        margin-top: 30px;
                        color: #cccccc;
                    }
                    .contact-info p {
                        margin: 10px 0;
                    }
                </style>
            </head>
            <body>
                <div class="maintenance-container">
                    <img src="' . plugins_url('logo.png', __FILE__) . '" alt="NYX-EI Logo" class="logo">
                    <h1>Site en Maintenance</h1>
                    <p>Nous effectuons actuellement des travaux de maintenance. Nous serons de retour très bientôt.</p>
                    <div class="contact-info">
                        <p>B.P 17623 Yaoundé</p>
                        <p>+237 697 99 15 90</p>
                        <p>contact@nyx-ei.tech</p>
                    </div>
                </div>
            </body>
            </html>',
            'Maintenance - NYX-EI',
            array('response' => 503)
        );
    }
}
add_action('get_header', 'nyx_ei_maintenance_mode');
