<?php

namespace App\Controllers;

use App\BaseController;
use App\Entity\Product;
use App\Tools;

class Controller extends BaseController {

    /**
     * @Route(path="/install", methods=["GET", "POST"])
     */
    private function install() {
        var_dump(Tools::installDatabase());
    }

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
        

        $test = new Product();
        var_dump($test->save());
    }
}