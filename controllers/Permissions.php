<?php
namespace JosephCrowell\Passage\Controllers;

use DB;
use Backend\Classes\Controller;
use Backend\Facades\BackendMenu;
use JosephCrowell\Passage\Models\Permission;
use JosephCrowell\Passage\Models\UserGroupsPermissions;
use System\Classes\PluginManager;
use Winter\User\Models\User;

/**
 * Permissions Back-end Controller
 */
class Permissions extends Controller
{
    public $addBtns = "";
    public $requiredPermissions = ["josephcrowell.passage.permissions"];
    public $implement = ["Backend.Behaviors.FormController", "Backend.Behaviors.ListController"];

    public $formConfig = "config_form.yaml";
    public $listConfig = "config_list.yaml";

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext("Winter.User", "user", "passage_permissions");
    }

    public function index()
    {
        parent::index();
        $manager       = PluginManager::instance();
        $this->addBtns = $manager->exists("shahiemseymor.roles")
            ? '
        <div class="layout-row">
            <div class="padded-container">
                <div class="callout callout-info">
                    <div class="header">
                    <p>It looks like you have "Frontend User Roles Manager" installed.<br />
                    I can try help you transfer data over to the "Passage Permissions" to save you time.<br />
                    For best results press red buttons in order from Left to Right.</p>
                    <a href="#"
                      data-request="onConvertFromPerms"
                      data-load-indicator="Loading..."
                      data-request-confirm="Are you sure you want all Permissions copied into Passage Permissions?"
                      class="btn btn-danger  oc-icon-exchange ">
                      (1) Transfer Permissions to Passage Permissions
                    </a>
                    <p>&nbsp;</p>

                    <a href="#"
                      data-request="onConvertFromRoles"
                      data-load-indicator="Loading..."
                      data-request-confirm="Are you sure you want all Roles copied into User Groups?"
                      class="btn btn-danger  oc-icon-exchange ">
                      (2) Transfer Roles to User Groups
                    </a><p>&nbsp;</p>

                    <a href="#"
                      data-request="onConvertFromRolesPerms"
                      data-load-indicator="Loading..."
                      data-request-confirm="Are you sure you want all Goup Permissions copied into Group Passage Permissions?"
                      class="btn btn-danger  oc-icon-exchange ">
                      (3) Transfer Goup Permissions to Group Passage Permissions
                    </a>
                    <p class="small">This notice will go away if you uninstall "Frontend User Roles Manager".</p>
                    </div>
                </div>
            </div>
        </div>'
            : "";
    }

    public function onConvertFromPerms()
    {
        $manager = PluginManager::instance();
        if ($manager->exists("shahiemseymor.roles"))
        {
            $perms = DB::table("shahiemseymor_permissions")->get();
            foreach ($perms as $perm)
            {
                $newRows[] = [
                    "id"          => $perm->id,
                    "name"        => $perm->name,
                    "description" => $perm->display_name,
                ];
            }
            Permission::insert($newRows);
        }
    }

    public function onConvertFromRoles()
    {
        $manager = PluginManager::instance();
        if ($manager->exists("shahiemseymor.roles"))
        {
            $roles = DB::table("shahiemseymor_roles")->get();
            foreach ($roles as $role)
            {
                $newRows[] = [
                    "id"          => $role->id,
                    "name"        => $role->name,
                    "code"        => str_replace(" ", "_", strtolower($role->name)),
                    "description" => $role->name,
                ];
            }

            \Winter\User\Models\UserGroup::insert($newRows);
        }
    }

    public function onConvertFromRolesPerms()
    {
        $manager = PluginManager::instance();
        if ($manager->exists("shahiemseymor.roles"))
        {
            $permRoles = DB::table("shahiemseymor_permission_role")->get();
            foreach ($permRoles as $pr)
            {
                $newRows[] = [
                    "permission_id" => $pr->permission_id,
                    "user_group_id" => $pr->role_id,
                ];
            }

            UserGroupsPermissions::insert($newRows);
        }
    }

    public static function userList($permission)
    {
        $query = User::whereHas("groups.passage_permissions", function ($q) use ($permission)
        {
            $q->where("permission_id", $permission);
        });

        return $query
            ->orderBy("surname")
            ->orderBy("name")
            ->get(["surname", "name", "email", "id"]);
    }

    public static function groupList($model)
    {
        return $model->groups;
    }
}
