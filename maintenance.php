<?php
/**
 * Plugin Name: NYX-EI Maintenance Mode
 * Plugin URI: https://nyx-ei.tech
 * Description: Un plugin de maintenance élégant pour NYX-EI
 * Version: 1.0
 * Author: NYX-EI
 * Author URI: https://nyx-ei.tech
 * Text Domain: nyx-maintenance
 */

// Si ce fichier est appelé directement, on sort
if (!defined('ABSPATH')) {
    exit;
}

// Définir le chemin du plugin
define('NYX_MAINTENANCE_PATH', plugin_dir_path(__FILE__));
define('NYX_MAINTENANCE_URL', plugin_dir_url(__FILE__));

class NyxMaintenanceMode {
    
    // Constructeur
    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
        add_action('template_redirect', array($this, 'maintenance_mode'));
        
        // Ajouter le lien "Paramètres" sur la page des plugins
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_settings_link'));
        
        // Enregistrer les styles et scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        
        // Créer le dossier uploads s'il n'existe pas
        $this->create_upload_directory();
        
        // Ajout de l'action pour l'upload du logo
        add_action('wp_ajax_nyx_upload_logo', array($this, 'handle_logo_upload'));
    }
    
    // Création du dossier d'upload
    private function create_upload_directory() {
        $upload_dir = wp_upload_dir();
        $nyx_upload_dir = $upload_dir['basedir'] . '/nyx-maintenance';
        
        if (!file_exists($nyx_upload_dir)) {
            wp_mkdir_p($nyx_upload_dir);
        }
    }
    
    // Lien vers paramètres dans la liste des plugins
    public function add_settings_link($links) {
        $settings_link = '<a href="options-general.php?page=nyx-maintenance">' . __('Paramètres', 'nyx-maintenance') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }
    
    // Ajouter la page de menu
    public function add_plugin_page() {
        add_options_page(
            'Mode Maintenance NYX-EI', 
            'Maintenance NYX-EI', 
            'manage_options', 
            'nyx-maintenance', 
            array($this, 'create_admin_page')
        );
    }
    
    // Options par défaut
    private function get_default_options() {
        return array(
            'enabled' => 0,
            'title' => 'We Are Coming Soon',
            'description' => 'We\'re strong believers that the best solutions come from gathering new insights and pushing conventional boundaries.',
            'date' => '',
            'logo' => '',
            'logo_width' => '150',
            'primary_color' => '#A6242F',
            'email' => 'contact@nyx-ei.tech',
            'phone' => '+237 697 99 15 90',
            'address' => 'B.P 17623 Yaoundé',
            'allowed_ips' => '',
            'exclude_urls' => '',
            'background_image' => '' // Nouvelle option pour l'image de fond
        );
    }
    
    // Créer la page d'administration
    public function create_admin_page() {
        $options = get_option('nyx_maintenance_option', $this->get_default_options());
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('nyx_maintenance_option_group');
                do_settings_sections('nyx-maintenance-admin');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
    
    // Initialiser les paramètres
    public function page_init() {
        register_setting(
            'nyx_maintenance_option_group',
            'nyx_maintenance_option',
            array($this, 'sanitize')
        );
        
        add_settings_section(
            'nyx_maintenance_setting_section',
            'Paramètres du mode maintenance',
            array($this, 'section_info'),
            'nyx-maintenance-admin'
        );
        
        add_settings_field(
            'enabled',
            'Activer le mode maintenance',
            array($this, 'enabled_callback'),
            'nyx-maintenance-admin',
            'nyx_maintenance_setting_section'
        );
        
        add_settings_field(
            'title',
            'Titre',
            array($this, 'title_callback'),
            'nyx-maintenance-admin',
            'nyx_maintenance_setting_section'
        );
        
        add_settings_field(
            'description',
            'Description',
            array($this, 'description_callback'),
            'nyx-maintenance-admin',
            'nyx_maintenance_setting_section'
        );
        
        add_settings_field(
            'date',
            'Date de lancement (laisser vide pour désactiver le compte à rebours)',
            array($this, 'date_callback'),
            'nyx-maintenance-admin',
            'nyx_maintenance_setting_section'
        );
        
        add_settings_field(
            'logo',
            'URL du logo',
            array($this, 'logo_callback'),
            'nyx-maintenance-admin',
            'nyx_maintenance_setting_section'
        );
        
        add_settings_field(
            'logo_width',
            'Largeur du logo (px)',
            array($this, 'logo_width_callback'),
            'nyx-maintenance-admin',
            'nyx_maintenance_setting_section'
        );
        
        // Ajout du champ pour l'image de fond
        add_settings_field(
            'background_image',
            'Image d\'arrière-plan',
            array($this, 'background_image_callback'),
            'nyx-maintenance-admin',
            'nyx_maintenance_setting_section'
        );
        
        add_settings_field(
            'primary_color',
            'Couleur principale',
            array($this, 'primary_color_callback'),
            'nyx-maintenance-admin',
            'nyx_maintenance_setting_section'
        );
        
        add_settings_field(
            'email',
            'Email de contact',
            array($this, 'email_callback'),
            'nyx-maintenance-admin',
            'nyx_maintenance_setting_section'
        );
        
        add_settings_field(
            'phone',
            'Téléphone',
            array($this, 'phone_callback'),
            'nyx-maintenance-admin',
            'nyx_maintenance_setting_section'
        );
        
        add_settings_field(
            'address',
            'Adresse',
            array($this, 'address_callback'),
            'nyx-maintenance-admin',
            'nyx_maintenance_setting_section'
        );
        
        add_settings_field(
            'allowed_ips',
            'IPs autorisées (séparées par des virgules)',
            array($this, 'allowed_ips_callback'),
            'nyx-maintenance-admin',
            'nyx_maintenance_setting_section'
        );
        
        add_settings_field(
            'exclude_urls',
            'URLs à exclure (une par ligne)',
            array($this, 'exclude_urls_callback'),
            'nyx-maintenance-admin',
            'nyx_maintenance_setting_section'
        );
    }
    
    // Sanitize des données
    public function sanitize($input) {
        $sanitized = array();
        $default_options = $this->get_default_options();
        
        $sanitized['enabled'] = isset($input['enabled']) ? 1 : 0;
        $sanitized['title'] = sanitize_text_field($input['title'] ?: $default_options['title']);
        $sanitized['description'] = sanitize_textarea_field($input['description'] ?: $default_options['description']);
        $sanitized['date'] = sanitize_text_field($input['date']);
        $sanitized['logo'] = esc_url_raw($input['logo']);
        $sanitized['logo_width'] = absint($input['logo_width'] ?: $default_options['logo_width']);
        $sanitized['primary_color'] = sanitize_hex_color($input['primary_color'] ?: $default_options['primary_color']);
        $sanitized['email'] = sanitize_email($input['email'] ?: $default_options['email']);
        $sanitized['phone'] = sanitize_text_field($input['phone'] ?: $default_options['phone']);
        $sanitized['address'] = sanitize_text_field($input['address'] ?: $default_options['address']);
        $sanitized['allowed_ips'] = sanitize_text_field($input['allowed_ips']);
        $sanitized['exclude_urls'] = sanitize_textarea_field($input['exclude_urls']);
        $sanitized['background_image'] = esc_url_raw($input['background_image']); // Sanitize de l'image de fond
        
        return $sanitized;
    }
    
    // Info Section
    public function section_info() {
        echo 'Configurez le mode maintenance de votre site';
    }
    
    // Callbacks des champs
    public function enabled_callback() {
        $options = get_option('nyx_maintenance_option', $this->get_default_options());
        printf(
            '<input type="checkbox" id="enabled" name="nyx_maintenance_option[enabled]" value="1" %s />',
            checked(1, $options['enabled'], false)
        );
    }
    
    public function title_callback() {
        $options = get_option('nyx_maintenance_option', $this->get_default_options());
        printf(
            '<input type="text" class="regular-text" id="title" name="nyx_maintenance_option[title]" value="%s" />',
            esc_attr($options['title'])
        );
    }
    
    public function description_callback() {
        $options = get_option('nyx_maintenance_option', $this->get_default_options());
        printf(
            '<textarea class="large-text" rows="3" id="description" name="nyx_maintenance_option[description]">%s</textarea>',
            esc_textarea($options['description'])
        );
    }
    
    public function date_callback() {
        $options = get_option('nyx_maintenance_option', $this->get_default_options());
        printf(
            '<input type="text" class="regular-text" id="date" name="nyx_maintenance_option[date]" value="%s" placeholder="YYYY-MM-DD HH:MM:SS" />',
            esc_attr($options['date'])
        );
        echo '<p class="description">Format: YYYY-MM-DD HH:MM:SS (ex: 2025-12-31 23:59:59)</p>';
    }
    
    public function logo_callback() {
        $options = get_option('nyx_maintenance_option', $this->get_default_options());
        $logo_url = esc_attr($options['logo']);
        
        echo '<div class="logo-upload-container">';
        printf(
            '<input type="text" class="regular-text" id="logo" name="nyx_maintenance_option[logo]" value="%s" />',
            $logo_url
        );
        echo '<button id="upload_logo_button" class="button">Sélectionner une image</button>';
        echo '<p class="description">Sélectionnez ou uploadez une image pour le logo</p>';
        
        if (!empty($logo_url)) {
            echo '<div class="logo-preview" style="margin-top: 10px;">';
            echo '<img id="logo_preview" src="' . $logo_url . '" style="max-width: 200px; height: auto;" />';
            echo '</div>';
        } else {
            echo '<div class="logo-preview" style="margin-top: 10px;">';
            echo '<img id="logo_preview" src="" style="max-width: 200px; height: auto; display: none;" />';
            echo '</div>';
        }
        echo '</div>';
    }
    
    // Callback pour l'image de fond
    public function background_image_callback() {
        $options = get_option('nyx_maintenance_option', $this->get_default_options());
        $bg_url = esc_attr($options['background_image']);
        
        echo '<div class="bg-upload-container">';
        printf(
            '<input type="text" class="regular-text" id="background_image" name="nyx_maintenance_option[background_image]" value="%s" />',
            $bg_url
        );
        echo '<button id="upload_bg_button" class="button">Sélectionner une image</button>';
        echo '<p class="description">Sélectionnez ou uploadez une image pour l\'arrière-plan (laissez vide pour utiliser le fond noir par défaut)</p>';
        
        if (!empty($bg_url)) {
            echo '<div class="bg-preview" style="margin-top: 10px;">';
            echo '<img id="bg_preview" src="' . $bg_url . '" style="max-width: 200px; height: auto;" />';
            echo '</div>';
        } else {
            echo '<div class="bg-preview" style="margin-top: 10px;">';
            echo '<img id="bg_preview" src="" style="max-width: 200px; height: auto; display: none;" />';
            echo '</div>';
        }
        echo '</div>';
    }
    
    public function logo_width_callback() {
        $options = get_option('nyx_maintenance_option', $this->get_default_options());
        printf(
            '<input type="number" min="50" max="500" id="logo_width" name="nyx_maintenance_option[logo_width]" value="%s" />',
            esc_attr($options['logo_width'])
        );
    }
    
    public function primary_color_callback() {
        $options = get_option('nyx_maintenance_option', $this->get_default_options());
        printf(
            '<input type="color" id="primary_color" name="nyx_maintenance_option[primary_color]" value="%s" />',
            esc_attr($options['primary_color'])
        );
    }
    
    public function email_callback() {
        $options = get_option('nyx_maintenance_option', $this->get_default_options());
        printf(
            '<input type="email" class="regular-text" id="email" name="nyx_maintenance_option[email]" value="%s" />',
            esc_attr($options['email'])
        );
    }
    
    public function phone_callback() {
        $options = get_option('nyx_maintenance_option', $this->get_default_options());
        printf(
            '<input type="text" class="regular-text" id="phone" name="nyx_maintenance_option[phone]" value="%s" />',
            esc_attr($options['phone'])
        );
    }
    
    public function address_callback() {
        $options = get_option('nyx_maintenance_option', $this->get_default_options());
        printf(
            '<input type="text" class="regular-text" id="address" name="nyx_maintenance_option[address]" value="%s" />',
            esc_attr($options['address'])
        );
    }
    
    public function allowed_ips_callback() {
        $options = get_option('nyx_maintenance_option', $this->get_default_options());
        printf(
            '<input type="text" class="large-text" id="allowed_ips" name="nyx_maintenance_option[allowed_ips]" value="%s" />',
            esc_attr($options['allowed_ips'])
        );
        echo '<p class="description">Les adresses IP listées auront accès au site malgré le mode maintenance.</p>';
    }
    
    public function exclude_urls_callback() {
        $options = get_option('nyx_maintenance_option', $this->get_default_options());
        printf(
            '<textarea class="large-text" rows="3" id="exclude_urls" name="nyx_maintenance_option[exclude_urls]">%s</textarea>',
            esc_textarea($options['exclude_urls'])
        );
        echo '<p class="description">Les URLs qui correspondent à ces chemins (une par ligne) resteront accessibles.</p>';
    }
    
    // Enregistrer les styles
    public function enqueue_styles() {
        if ($this->is_maintenance_active()) {
            wp_enqueue_style('google-font-montserrat', 'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap', array(), null);
        }
    }
    
    // Enregistrer les scripts et styles d'admin
    public function admin_enqueue_scripts($hook) {
        if ($hook === 'settings_page_nyx-maintenance') {
            wp_enqueue_media();
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');
            
            wp_enqueue_script(
                'nyx-maintenance-admin', 
                NYX_MAINTENANCE_URL . 'admin.js', 
                array('jquery', 'wp-color-picker', 'media-upload'), 
                time(), 
                true
            );
            
            // Créer le fichier JS si nécessaire
            $this->create_admin_js_file();
        }
    }
    
    // Créer le fichier JavaScript d'administration
    private function create_admin_js_file() {
        $js_file = NYX_MAINTENANCE_PATH . 'admin.js';
        
        if (!file_exists($js_file)) {
            $js_content = '
jQuery(document).ready(function($) {
    // Initialiser le color picker
    $(".color-picker").wpColorPicker();
    
    // Gestion de l\'upload du logo
    $("#upload_logo_button").on("click", function(e) {
        e.preventDefault();
        var custom_uploader = wp.media({
            title: "Sélectionner une image pour le logo",
            button: {
                text: "Utiliser cette image"
            },
            multiple: false
        })
        .on("select", function() {
            var attachment = custom_uploader.state().get("selection").first().toJSON();
            $("#logo").val(attachment.url);
            $("#logo_preview").attr("src", attachment.url).show();
        })
        .open();
    });
    
    // Gestion de l\'upload de l\'arrière-plan
    $("#upload_bg_button").on("click", function(e) {
        e.preventDefault();
        var custom_uploader = wp.media({
            title: "Sélectionner une image pour l\'arrière-plan",
            button: {
                text: "Utiliser cette image"
            },
            multiple: false
        })
        .on("select", function() {
            var attachment = custom_uploader.state().get("selection").first().toJSON();
            $("#background_image").val(attachment.url);
            $("#bg_preview").attr("src", attachment.url).show();
        })
        .open();
    });
    
    // Afficher ou masquer l\'aperçu du logo
    if($("#logo").val()) {
        $("#logo_preview").attr("src", $("#logo").val()).show();
    } else {
        $("#logo_preview").hide();
    }
    
    // Afficher ou masquer l\'aperçu de l\'arrière-plan
    if($("#background_image").val()) {
        $("#bg_preview").attr("src", $("#background_image").val()).show();
    } else {
        $("#bg_preview").hide();
    }
});';
            file_put_contents($js_file, $js_content);
        }
    }
    
    // Gérer l'upload du logo
    public function handle_logo_upload() {
        check_ajax_referer('nyx_maintenance_nonce', 'security');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permissions insuffisantes');
        }
        
        if (!isset($_FILES['file'])) {
            wp_send_json_error('Aucun fichier reçu');
        }
        
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['basedir'] . '/nyx-maintenance/';
        $upload_url = $upload_dir['baseurl'] . '/nyx-maintenance/';
        
        $file_name = sanitize_file_name($_FILES['file']['name']);
        $destination = $upload_path . $file_name;
        
        if (move_uploaded_file($_FILES['file']['tmp_name'], $destination)) {
            wp_send_json_success(array(
                'url' => $upload_url . $file_name
            ));
        } else {
            wp_send_json_error('Erreur lors de l\'upload du fichier');
        }
    }
    
    // Vérifier si le mode maintenance est actif pour l'utilisateur actuel
    private function is_maintenance_active() {
        $options = get_option('nyx_maintenance_option', $this->get_default_options());
        
        // Si désactivé, retourner false
        if (!$options['enabled']) {
            return false;
        }
        
        // Admin connecté, pas de maintenance
        if (current_user_can('manage_options')) {
            return false;
        }
        
        // Vérification des IPs autorisées
        if (!empty($options['allowed_ips'])) {
            $allowed_ips = array_map('trim', explode(',', $options['allowed_ips']));
            $user_ip = $this->get_user_ip();
            
            if (in_array($user_ip, $allowed_ips)) {
                return false;
            }
        }
        
        // Vérification des URLs exclues
        if (!empty($options['exclude_urls'])) {
            $current_url = $_SERVER['REQUEST_URI'];
            $exclude_urls = explode("\n", str_replace("\r", "", $options['exclude_urls']));
            
            foreach ($exclude_urls as $url) {
                $url = trim($url);
                if (!empty($url) && strpos($current_url, $url) !== false) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    // Obtenir l'IP de l'utilisateur
    private function get_user_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
    
    // Activer le mode maintenance
    public function maintenance_mode() {
        if ($this->is_maintenance_active()) {
            $options = get_option('nyx_maintenance_option', $this->get_default_options());
            
            // Définir le statut HTTP
            status_header(503);
            header('Retry-After: 3600');
            
            // Afficher la page de maintenance
            $this->display_maintenance_page($options);
            exit;
        }
    }
    
    // Afficher la page de maintenance
    private function display_maintenance_page($options) {
        // Extraire les options
        $title = $options['title'];
        $description = $options['description'];
        $date = $options['date'];
        $logo = $options['logo'];
        $logo_width = $options['logo_width'];
        $primary_color = $options['primary_color'];
        $email = $options['email'];
        $phone = $options['phone'];
        $address = $options['address'];
        $background_image = $options['background_image'];
        
        // Générer la page de maintenance
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo get_bloginfo('name'); ?> - Mode Maintenance</title>
            <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap">
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                
                body {
                    font-family: 'Montserrat', sans-serif;
                    background-color: #000;
                    <?php if (!empty($background_image)): ?>
                    background-image: url('<?php echo esc_url($background_image); ?>');
                    background-size: cover;
                    background-position: center;
                    background-repeat: no-repeat;
                    background-attachment: fixed;
                    <?php endif; ?>
                    color: #fff;
                    line-height: 1.6;
                    min-height: 100vh;
                    display: flex;
                    flex-direction: column;
                }
                
                .container {
                    width: 100%;
                    max-width: 1200px;
                    margin: 0 auto;
                    padding: 20px;
                    flex: 1;
                    display: flex;
                    flex-direction: column;
                    <?php if (!empty($background_image)): ?>
                    background-color: rgba(0, 0, 0, 0.7); /* Fond semi-transparent pour l'image */
                    <?php endif; ?>
                }
                
                header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 20px 0;
                }
                
                .logo {
                    display: flex;
                    align-items: center;
                }
                
                .logo img {
                    max-width: <?php echo esc_attr($logo_width); ?>px;
                    height: auto;
                }
                
                .contact-info {
                    text-align: right;
                    font-size: 14px;
                }
                
                main {
                    padding: 60px 0;
                    display: flex;
                    flex: 1;
                    flex-direction: column;
                    justify-content: center;
                }
                
                h1 {
                    font-size: 56px;
                    font-weight: 700;
                    line-height: 1.2;
                    margin-bottom: 40px;
                }
                
                .countdown-container {
                    display: flex;
                    gap: 20px;
                    margin: 40px 0;
                }
                
                .countdown-box {
                    text-align: center;
                }
                
                .countdown-value {
                    font-size: 48px;
                    font-weight: 700;
                }
                
                .countdown-separator {
                    font-size: 48px;
                    margin: 0 10px;
                    align-self: flex-start;
                }
                
                .countdown-label {
                    font-size: 14px;
                    color: #aaa;
                    margin-top: 10px;
                }
                
                .description {
                    max-width: 600px;
                    font-size: 16px;
                    line-height: 1.8;
                    color: #aaa;
                }
                
                .primary-color {
                    color: <?php echo esc_attr($primary_color); ?>;
                }
                
                .content-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 40px;
                    align-items: center;
                }
                
                @media (max-width: 768px) {
                    header {
                        flex-direction: column;
                        text-align: center;
                    }
                    
                    .contact-info {
                        text-align: center;
                        margin-top: 20px;
                    }
                    
                    .content-grid {
                        grid-template-columns: 1fr;
                    }
                    
                    h1 {
                        font-size: 36px;
                    }
                    
                    .countdown-value {
                        font-size: 32px;
                    }
                    
                    .countdown-separator {
                        font-size: 32px;
                    }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <header>
                    <div class="logo">
                        <?php if (!empty($logo)): ?>
                            <img src="<?php echo esc_url($logo); ?>" alt="NYX-EI Logo">
                        <?php else: ?>
                            <h3>NYX-EI</h3>
                        <?php endif; ?>
                    </div>
                    <div class="contact-info">
                        <?php echo esc_html($email); ?>
                    </div>
                </header>
                
                <main>
                    <div class="content-grid">
                        <div>
                            <h1><?php echo esc_html($title); ?></h1>
                            
                            <?php if (!empty($date)): ?>
                                <div class="countdown-container">
                                    <div class="countdown-box">
                                        <div id="days" class="countdown-value">00</div>
                                        <div class="countdown-label">Days</div>
                                    </div>
                                    <div class="countdown-separator">:</div>
                                    <div class="countdown-box">
                                        <div id="minutes" class="countdown-value">00</div>
                                        <div class="countdown-label">Minutes</div>
                                    </div>
                                    <div class="countdown-separator">:</div>
                                    <div class="countdown-box">
                                        <div id="seconds" class="countdown-value">00</div>
                                        <div class="countdown-label">Seconds</div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div>
                            <p class="description"><?php echo esc_html($description); ?></p>
                            <div style="margin-top: 40px;">
                                <p><strong>Contact:</strong></p>
                                <p><?php echo esc_html($address); ?></p>
                                <p><?php echo esc_html($phone); ?></p>
                                <p><?php echo esc_html($email); ?></p>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
            
            <?php if (!empty($date)): ?>
                <script>
                    // Fonction de compte à rebours
                    function updateCountdown() {
                        const targetDate = new Date('<?php echo esc_js($date); ?>').getTime();
                        const now = new Date().getTime();
                        const difference = targetDate - now;
                        
                        if (difference <= 0) {
                            document.getElementById('days').innerText = '00';
                            document.getElementById('hours').innerText = '00';
                            document.getElementById('minutes').innerText = '00';
                            document.getElementById('seconds').innerText = '00';
                            return;
                        }
                        
                        const days = Math.floor(difference / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((difference % (1000 * 60)) / 1000);
                        
                        document.getElementById('days').innerText = days < 10 ? '0' + days : days;
                        document.getElementById('hours').innerText = hours < 10 ? '0' + hours : hours;
                        document.getElementById('minutes').innerText = minutes < 10 ? '0' + minutes : minutes;
                        document.getElementById('seconds').innerText = seconds < 10 ? '0' + seconds : seconds;
                    }
                    
                    // Mettre à jour toutes les secondes
                    setInterval(updateCountdown, 1000);
                    
                    // Appel initial
                    updateCountdown();
                </script>
            <?php endif; ?>
        </body>
        </html>
        <?php
    }
}

// Initialiser le plugin
new NyxMaintenanceMode();
