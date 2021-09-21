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

    /**
     * @Route(path="/test/:id", methods=["GET"])
     */
    private function test($id) {
        echo $id;
    }

    /**
     * @Route(path="/test/:id/:chaine", methods=["GET"])
     */
    private function chaine($id, $chaine) {
        echo $id.' : '.$chaine.'<br>';
        echo 'Get : '.$_GET['s'];
    }
}