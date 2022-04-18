<?php

namespace App\Controllers;

use App\BaseController;
use App\Installer;

class InstallerController extends BaseController {
    /**
     * @Route(path="/:path", methods=["GET"])
     */
    private function install($path) {
        Installer::install();
    }
}