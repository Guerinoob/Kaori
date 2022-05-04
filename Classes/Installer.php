<?php

namespace App;

/**
 * This class serves as an installer of the framework. It creates the custom files needed for configuration
 */
class Installer {

    /**
     * Installs Kaori
     */
    public static function install() {
        $root_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

        $f = fopen(DOCUMENT_ROOT.'/custom_defines.php', 'w+');
        $content = "
            <?php
            define('DB_USER', '');
            define('DB_PASSWORD', '');
            define('DB_HOST', '');
            define('DB_NAME', '');

            define('SITENAME', 'Kaori');

            define('ROOT_URL', '".$root_url."');

            define('SOCKET_PORT', 1000);

            define('EMAIL', '');
            define('SMTP_HOST', '');
            define('SMTP_PORT', 25);
            define('SMTP_USERNAME', '');
            define('SMTP_PASSWORD', '');
        ";

        fwrite($f,  str_replace("    ", "", trim($content)));
        fclose($f);

        unlink(DOCUMENT_ROOT.'/Controllers/InstallerController.php');

    }
}