<?php
namespace JosephCrowell\Passage;

use App, Event;
use Backend\Facades\Backend;
use Backend\Facades\BackendAuth;
use Illuminate\Foundation\AliasLoader;
use JosephCrowell\Passage\Classes\PermissionsService as PassageService;
use System\Classes\PluginBase;
use Winter\User\Controllers\UserGroups;
use Winter\User\Models\UserGroup;

/**
 * Passage Plugin Information File
 */
class Plugin extends PluginBase
{
    public static $permissions = null;
    public static $groups = null;

    public $require = ["Winter.User"];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            "name" => "passage",
            "description" => "Fast, Efficient permission system for controlling access to your website resources.",
            "author" => "Joseph Crowell",
            "icon" => "icon-key",
        ];
    }

    public function messageURL()
    {
        return "https://computerlink.com.au/notices/";
    }

    public function boot()
    {
        UserGroup::extend(function ($model) {
            $model->addFillable(["core_group"]);

            $model->belongsToMany["passage_permissions"] = [
                "JosephCrowell\Passage\Models\Permission",
                "table" => "josephcrowell_passage_groups_permissions",
                "key" => "user_group_id",
                "otherKey" => "permission_id",
                "order" => "name",
            ];
        });

        UserGroups::extend(function ($controller) {
            $controller->implement[] = "JosephCrowell.Passage.Behaviors.PermissionCopy";
        });

        Event::listen("backend.menu.extendItems", function ($manager) {
            $manager->addSideMenuItems("Winter.User", "user", [
                "usergroups" => [
                    "label" => "winter.user::lang.groups.all_groups",
                    "icon" => "icon-group",
                    "code" => "u_groups",
                    "owner" => "Winter.User",
                    "url" => Backend::url("winter/user/usergroups"),
                ],
                "passage_permissions" => [
                    "label" => "josephcrowell.passage::lang.plugin.backend_menu",
                    "icon" => "icon-key",
                    "order" => 1001,
                    "code" => "passage",
                    "owner" => "Winter.User",
                    "permissions" => ["josephcrowell.passage.*"],
                    "url" => Backend::url("josephcrowell/passage/permissions"),
                ],
                "override" => [
                    "label" => "josephcrowell.passage::lang.plugin.backend_override",
                    "icon" => "icon-key",
                    "order" => 1002,
                    "code" => "passage",
                    "owner" => "Winter.User",
                    "permissions" => ["josephcrowell.passage.*"],
                    "url" => Backend::url("josephcrowell/passage/overrides"),
                ],
            ]);
        });

        Event::listen("backend.form.extendFields", function ($widget) {
            $UGcontroller = $widget->getController();
            if (! $UGcontroller instanceof UserGroups) {
                return;
            }

            if (! $widget->model instanceof UserGroup) {
                return;
            }
            //die(BackendAuth::getUser()->first_name);
            if (! BackendAuth::getUser()->hasAccess("josephcrowell.passage.usergroups")) {
                return;
            }

            $UGcontroller->getAllGroups();

            $widget->addFields(
                [
                    "passage_permissions" => [
                        "tab" => "josephcrowell.passage::lang.plugin.field_tab",
                        "label" => "josephcrowell.passage::lang.plugin.field_label",
                        "commentAbove" => "josephcrowell.passage::lang.plugin.field_commentAbove",
                        "span" => "left",
                        "type" => "relation",
                        "emptyOption" => "josephcrowell.passage::lang.plugin.field_emptyOption",
                    ],
                    "copy_btn" => [
                        "tab" => "josephcrowell.passage::lang.plugin.field_tab",
                        "label" => "josephcrowell.passage::lang.copy",
                        "commentAbove" => "josephcrowell.passage::lang.copy_comment",
                        "span" => "right",
                        "type" => "partial",
                        "path" => '$/josephcrowell/passage/controllers/permissions/_copy.htm',
                    ],
                ],
                "primary"
            );
        });

        $alias = AliasLoader::getInstance();
        $alias->alias("PassageService", "\JosephCrowell\Passage\Classes\PermissionsService");
        App::register("\JosephCrowell\Passage\Services\PassageServiceProvider");
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            "josephcrowell.passage.*" => [
                "tab" => "Winter.User::lang.plugin.tab",
                "label" => "josephcrowell.passage::lang.plugin.permission_label",
            ],

            "josephcrowell.passage.usergroups" => [
                "tab" => "Winter.User::lang.plugin.tab",
                "label" => "josephcrowell.passage::lang.plugin.permission_label_ug",
            ],
        ];
    }

    public function registerMarkupTags()
    {
        return [
            "functions" => [
                "can" => function ($permission) {
                    return PassageService::hasPermissionName($permission);
                },
                "hasPermissionName" => function ($permission) {
                    return PassageService::hasPermissionName($permission);
                },
                "hasPermissionNames" => function ($permissions) {
                    return PassageService::hasPermissionNames($permissions);
                },
                "hasPermission" => function ($permission_id) {
                    return PassageService::hasPermission($permission_id);
                },
                "hasPermissions" => function ($permission_ids) {
                    return PassageService::hasPermissions($permission_ids);
                },

                "inGroupName" => function ($group) {
                    return PassageService::inGroupName($group);
                },
                "inGroupNames" => function ($groups) {
                    return PassageService::inGroupNames($groups);
                },
                "inGroup" => function ($group_permission) {
                    return PassageService::inGroup($group_permission);
                },
                "inGroups" => function ($group_permissions) {
                    return PassageService::inGroups($group_permissions);
                },
            ],
        ];
    }
}
