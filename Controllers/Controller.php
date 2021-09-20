<?php

namespace App\Controllers;

use BaseController;

class Controller extends BaseController {

    /**
     * @Route(path="/index", methods=["GET", "POST"])
     */
    private function index() {
        echo 'Index';
    }
}