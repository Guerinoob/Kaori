<?php

namespace App\Controllers;

use App\BaseController;
use App\Installer;

class InstallerController extends BaseController {
    /**
     * @Route(path="/install", methods=["GET"])
     */
    private function install() {
        Installer::install();
    }
}