<?php

namespace App\Controllers;

use App\BaseController;
use App\Entity\Category;
use App\Entity\Product;
use App\Tools;

class Controller extends BaseController {

    private $test;

    /**
     * @Route(path="/", methods=["GET", "POST"])
     */
    private function er() {
        $this->assign('nom', 'Kaori');
        $this->render(TEMPLATES_PATH.'/index.php');
    }

    /**
     * @Route(path="/install", methods=["GET", "POST"])
     */
    private function install() {
        var_dump(Tools::installDatabase());
    }

    /**
     * @Route(path="/category/add/:name", methods=["GET", "POST"])
     */
    private function index($name) {
        $cat = new Category();
        $cat->setName($name);
        $cat->save();
    }

    /**
     * @Route(path="/category/", methods=["GET"])
     */
    private function test() {
        var_dump(Category::getAll());
    }

    /**
     * @Route(path="/category/:id", methods=["GET"])
     */
    private function chaine($id) {
        var_dump(Category::getBy([
                'id' => ['equal' => '=', 'value' => '1'], 
                'name' => ['operator' => 'OR', 'equal' => 'LIKE', 'value' => 'Jar%']
            ]
        ));
    }
}