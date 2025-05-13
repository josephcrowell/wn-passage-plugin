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

    public $require = ['Winter.User'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name' => 'passage',
            'description' => 'Fast, Efficient permission system for controlling access to your website resources.',
            'author' => 'Joseph Crowell',
            'icon' => 'icon-key',
        ];
    }

    public function messageURL()
    {
        return 'https://computerlink.com.au/notices/';
    }

    public function boot()
    {
        User::extend(function ($model) {
            $model->addDynamicMethod('hasPermission', function (int $permission_id) use ($model) {
                $add = $subtract = [];
                $overrides = Override::where("user_id", $model->id)->get(["user_id", "permission_id", "grant"]);
                foreach ($overrides as $override) {
                    if ($override->grant) {
                        $add[] = $override->permission_id;
                    } else {
                        $subtract[] = $override->permission_id;
                    }
                }

                $query = Permission::whereHas("groups.users", function ($q) use ($model) {
                    $q->where("user_id", $model->id);
                });
                if ($subtract) {
                    $query->whereNotIn("id", $subtract);
                }
                if ($add) {
                    $query->orWhereIn("id", $add);
                }
                $permissions = $query->lists("name", "id");

                return in_array($permission_id, $permissions);
            });

            $model->addDynamicMethod('hasPermissionName', function (string $permission_name) use ($model) {
                $add = $subtract = [];
                $overrides = Override::where("user_id", $model->id)->get(["user_id", "permission_id", "grant"]);
                foreach ($overrides as $override) {
                    if ($override->grant) {
                        $add[] = $override->permission_id;
                    } else {
                        $subtract[] = $override->permission_id;
                    }
                }

                $query = Permission::whereHas("groups.users", function ($q) use ($model) {
                    $q->where("user_id", $model->id);
                });
                if ($subtract) {
                    $query->whereNotIn("id", $subtract);
                }
                if ($add) {
                    $query->orWhereIn("id", $add);
                }
                $permissions = $query->lists("name", "id");

                return in_array($permission_name, $permissions);
            });

            $model->addDynamicMethod('hasPermissions', function (array $check_permission_ids) use ($model) {
                $add = $subtract = [];
                $overrides = Override::where("user_id", $model->id)->get(["user_id", "permission_id", "grant"]);
                foreach ($overrides as $override) {
                    if ($override->grant) {
                        $add[] = $override->permission_id;
                    } else {
                        $subtract[] = $override->permission_id;
                    }
                }

                $query = Permission::whereHas("groups.users", function ($q) use ($model) {
                    $q->where("user_id", $model->id);
                });
                if ($subtract) {
                    $query->whereNotIn("id", $subtract);
                }
                if ($add) {
                    $query->orWhereIn("id", $add);
                }
                $permissions = array_flip($query->lists("name", "id"));

                return count(array_intersect($check_permission_ids, $permissions)) == count($check_permission_ids);
            });

            $model->addDynamicMethod('hasPermissionNames', function (array $check_permissions) use ($model) {
                $add = $subtract = [];
                $overrides = Override::where("user_id", $model->id)->get(["user_id", "permission_id", "grant"]);
                foreach ($overrides as $override) {
                    if ($override->grant) {
                        $add[] = $override->permission_id;
                    } else {
                        $subtract[] = $override->permission_id;
                    }
                }

                $query = Permission::whereHas("groups.users", function ($q) use ($model) {
                    $q->where("user_id", $model->id);
                });
                if ($subtract) {
                    $query->whereNotIn("id", $subtract);
                }
                if ($add) {
                    $query->orWhereIn("id", $add);
                }
                $permissions = $query->lists("name", "id");

                return count(array_intersect($check_permissions, $permissions)) == count($check_permissions);
            });

            $model->addDynamicMethod('inGroup', function (string $group_code) use ($model) {
                $groups = $model->groups->lists("name", "code");

                return array_key_exists($group_code, $groups);
            });

            $model->addDynamicMethod('inGroupName', function (string $group_name) use ($model) {
                $groups = $model->groups->lists("name", "code");

                return in_array($group_name, $groups);
            });

            $model->addDynamicMethod('inGroups', function (array $check_group_codes) use ($model) {
                $group_codes = array_flip($model->groups->lists("name", "code"));

                return count(array_intersect($check_group_codes, $group_codes)) == count($check_group_codes);
            });

            $model->addDynamicMethod('inGroupNames', function (array $check_groups) use ($model) {
                $group_names = $model->groups->lists("name", "code");

                return count(array_intersect($check_groups, $group_names)) == count($check_groups);
            });
        });

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
            $controller->implement[] = 'JosephCrowell.Passage.Behaviors.PermissionCopy';
        });

        Event::listen('backend.menu.extendItems', function ($manager) {
            $manager->addSideMenuItems('Winter.User', 'user', [
                'usergroups' => [
                    'label' => 'winter.user::lang.groups.all_groups',
                    'icon' => 'icon-group',
                    'code' => 'u_groups',
                    'owner' => 'Winter.User',
                    'url' => Backend::url('winter/user/usergroups'),
                ],
                'passage_permissions' => [
                    'label' => 'josephcrowell.passage::lang.plugin.backend_menu',
                    'icon' => 'icon-key',
                    'order' => 1001,
                    'code' => 'passage',
                    'owner' => 'Winter.User',
                    'permissions' => ['josephcrowell.passage.*'],
                    'url' => Backend::url('josephcrowell/passage/permissions'),
                ],
                'override' => [
                    'label' => 'josephcrowell.passage::lang.plugin.backend_override',
                    'icon' => 'icon-key',
                    'order' => 1002,
                    'code' => 'passage',
                    'owner' => 'Winter.User',
                    'permissions' => ['josephcrowell.passage.*'],
                    'url' => Backend::url('josephcrowell/passage/overrides'),
                ],
            ]);
        });

        Event::listen('backend.form.extendFields', function ($widget) {
            $UGcontroller = $widget->getController();
            if (! $UGcontroller instanceof UserGroups) {
                return;
            }

            if (! $widget->model instanceof UserGroup) {
                return;
            }
            //die(BackendAuth::getUser()->first_name);
            if (! BackendAuth::getUser()->hasAccess('josephcrowell.passage.usergroups')) {
                return;
            }

            $UGcontroller->getAllGroups();

            $widget->addFields(
                [
                    'passage_permissions' => [
                        'tab' => 'josephcrowell.passage::lang.plugin.field_tab',
                        'label' => 'josephcrowell.passage::lang.plugin.field_label',
                        'commentAbove' => 'josephcrowell.passage::lang.plugin.field_commentAbove',
                        'span' => 'left',
                        'type' => 'relation',
                        'emptyOption' => 'josephcrowell.passage::lang.plugin.field_emptyOption',
                    ],
                    'copy_btn' => [
                        'tab' => 'josephcrowell.passage::lang.plugin.field_tab',
                        'label' => 'josephcrowell.passage::lang.copy',
                        'commentAbove' => 'josephcrowell.passage::lang.copy_comment',
                        'span' => 'right',
                        'type' => 'partial',
                        'path' => '$/josephcrowell/passage/controllers/permissions/_copy.htm',
                    ],
                ],
                'primary'
            );
        });

        $alias = AliasLoader::getInstance();
        $alias->alias('PassageService', '\JosephCrowell\Passage\Classes\PermissionsService');
        App::register('\JosephCrowell\Passage\Services\PassageServiceProvider');
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'josephcrowell.passage.*' => [
                'tab' => 'Winter.User::lang.plugin.tab',
                'label' => 'josephcrowell.passage::lang.plugin.permission_label',
            ],

            'josephcrowell.passage.usergroups' => [
                'tab' => 'Winter.User::lang.plugin.tab',
                'label' => 'josephcrowell.passage::lang.plugin.permission_label_ug',
            ],
        ];
    }

    public function registerMarkupTags()
    {
        return [
            'functions' => [
                'can' => function ($permission) {
                    return PassageService::hasPermissionName($permission);
                },
                'hasPermissionName' => function ($permission) {
                    return PassageService::hasPermissionName($permission);
                },
                'hasPermissionNames' => function ($permissions) {
                    return PassageService::hasPermissionNames($permissions);
                },
                'hasPermission' => function ($permission_id) {
                    return PassageService::hasPermission($permission_id);
                },
                'hasPermissions' => function ($permission_ids) {
                    return PassageService::hasPermissions($permission_ids);
                },

                'inGroupName' => function ($group) {
                    return PassageService::inGroupName($group);
                },
                'inGroupNames' => function ($groups) {
                    return PassageService::inGroupNames($groups);
                },
                'inGroup' => function ($group_permission) {
                    return PassageService::inGroup($group_permission);
                },
                'inGroups' => function ($group_permissions) {
                    return PassageService::inGroups($group_permissions);
                },
            ],
        ];
    }
}
