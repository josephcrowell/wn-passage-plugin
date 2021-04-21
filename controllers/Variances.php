<?php

namespace JosephCrowell\Passage\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Variances Back-end Controller
 */
class Variances extends Controller
{
    public $implement = [
        "Backend.Behaviors.FormController",
        "Backend.Behaviors.ListController",
    ];

    public $formConfig = "config_form.yaml";
    public $listConfig = "config_list.yaml";

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext("Winter.User", "user", "variances");
    }
}