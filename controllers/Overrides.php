<?php
namespace JosephCrowell\Passage\Controllers;

use Backend\Classes\Controller;
use Backend\Facades\BackendMenu;

/**
 * Overrides Back-end Controller
 */
class Overrides extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Winter.User', 'user', 'overrides');
    }
}
