<?php

namespace JosephCrowell\Passage;

use App, Backend, BackendAuth, Event, Yaml, File;
use System\Classes\PluginBase;
use Illuminate\Foundation\AliasLoader;
use Winter\User\Controllers\Users as UsersController;
use Winter\User\Controllers\UserGroups as UserGroupsController;
use Winter\User\Models\User as UserModel;
use Winter\User\Models\UserGroup as UserGroupModel;
use Winter\Notify\NotifyRules\SaveDatabaseAction;
use Winter\User\Classes\UserEventBase;

/**
 * passage Plugin Information File
 */
class Plugin extends PluginBase
{
    public static $keys = null;
    public static $groups = null;

    public $require = ["Winter.User", "Winter.Location", "Winter.Notify"];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            "name" => "passage",
            "description" =>
                "Fast, Efficient permission system for controlling access to your website resources.",
            "author" => "JosephCrowell",
            "icon" => "icon-key",
            "replaces" => "KurtJensen.Passage",
        ];
    }

    public function messageURL()
    {
        return "http://firemankurt.com/notices/";
    }

    public function boot()
    {
        UserModel::extend(function ($model) {
            $model->addFillable([
                "phone",
                "mobile",
                "company",
                "street_addr",
                "city",
                "zip",
            ]);

            $model->implement[] = "Winter.Location.Behaviors.LocationModel";

            $model->morphMany["notifications"] = [
                NotificationModel::class,
                "name" => "notifiable",
                "order" => "created_at desc",
            ];
        });

        UserGroupModel::extend(function ($model) {
            $model->belongsToMany["passage_keys"] = [
                "JosephCrowell\Passage\Models\Key",
                "table" => "josephcrowell_passage_groups_keys",
                "key" => "user_group_id",
                "otherKey" => "key_id",
                "order" => "name",
            ];
        });

        UsersController::extendFormFields(function ($widget) {
            // Prevent extending of related form instead of the intended User form
            if (!$widget->model instanceof UserModel) {
                return;
            }

            $configFile = plugins_path(
                "josephcrowell/passage/config/profile_fields.yaml"
            );
            $config = Yaml::parse(File::get($configFile));
            $widget->addTabFields($config);
        });

        UserGroupsController::extend(function ($controller) {
            $controller->implement[] =
                "JosephCrowell.Passage.Behaviors.KeyCopy";
        });

        SaveDatabaseAction::extend(function ($action) {
            $action->addTableDefinition([
                "label" => "User activity",
                "class" => UserModel::class,
                "param" => "user",
            ]);
        });

        UserEventBase::extend(function ($event) {
            $event->conditions[] =
                \JosephCrowell\Passage\NotifyRules\UserLocationAttributeCondition::class;
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
                "passage_keys" => [
                    "label" =>
                        "josephcrowell.passage::lang.plugin.backend_menu",
                    "icon" => "icon-key",
                    "order" => 1001,
                    "code" => "passage",
                    "owner" => "Winter.User",
                    "permissions" => ["josephcrowell.passage.*"],
                    "url" => Backend::url("josephcrowell/passage/keys"),
                ],
                "variance" => [
                    "label" =>
                        "josephcrowell.passage::lang.plugin.backend_variance",
                    "icon" => "icon-key",
                    "order" => 1002,
                    "code" => "passage",
                    "owner" => "Winter.User",
                    "permissions" => ["josephcrowell.passage.*"],
                    "url" => Backend::url("josephcrowell/passage/variances"),
                ],
            ]);
        });

        Event::listen("backend.form.extendFields", function ($widget) {
            $UGcontroller = $widget->getController();
            if (!$UGcontroller instanceof \Winter\User\Controllers\UserGroups) {
                return;
            }

            if (!$widget->model instanceof \Winter\User\Models\UserGroup) {
                return;
            }
            //die(BackendAuth::getUser()->first_name);
            if (
                !BackendAuth::getUser()->hasAccess(
                    "josephcrowell.passage.usergroups"
                )
            ) {
                return;
            }

            $UGcontroller->getAllGroups();

            $widget->addFields(
                [
                    "passage_keys" => [
                        "tab" => "josephcrowell.passage::lang.plugin.field_tab",
                        "label" =>
                            "josephcrowell.passage::lang.plugin.field_label",
                        "commentAbove" =>
                            "josephcrowell.passage::lang.plugin.field_commentAbove",
                        "span" => "left",
                        "type" => "relation",
                        "emptyOption" =>
                            "josephcrowell.passage::lang.plugin.field_emptyOption",
                    ],
                    "copy_btn" => [
                        "tab" => "josephcrowell.passage::lang.plugin.field_tab",
                        "label" => "josephcrowell.passage::lang.copy",
                        "commentAbove" =>
                            "josephcrowell.passage::lang.copy_comment",
                        "span" => "right",
                        "type" => "partial",
                        "path" =>
                            '$/josephcrowell/passage/controllers/keys/_copy.htm',
                    ],
                ],
                "primary"
            );
        });

        $alias = AliasLoader::getInstance();
        $alias->alias(
            "PassageService",
            "\JosephCrowell\Passage\Classes\KeyRing"
        );
        App::register("\JosephCrowell\Passage\Services\PassageServiceProvider");
    }

    public function registerComponents()
    {
        return [
            \JosephCrowell\Passage\Components\Notifications::class =>
                "notifications",
        ];
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
                "label" => "josephcrowell.passage::lang.plugin.permiss_label",
            ],

            "josephcrowell.passage.usergroups" => [
                "tab" => "Winter.User::lang.plugin.tab",
                "label" =>
                    "josephcrowell.passage::lang.plugin.permiss_label_ug",
            ],
        ];
    }

    public function registerMarkupTags()
    {
        return [
            "functions" => [
                "can" => function ($key) {
                    return app("PassageService")::hasKeyName($key);
                },
                "hasKeyName" => function ($key) {
                    return app("PassageService")::hasKeyName($key);
                },
                "hasKeyNames" => function ($keys) {
                    return app("PassageService")::hasKeyNames($keys);
                },
                "hasKey" => function ($key_id) {
                    return app("PassageService")::hasKey($key_id);
                },
                "hasKeys" => function ($key_ids) {
                    return app("PassageService")::hasKeys($key_ids);
                },

                "inGroupName" => function ($group) {
                    return app("PassageService")::inGroupName($group);
                },
                "inGroupNames" => function ($groups) {
                    return app("PassageService")::inGroupNames($groups);
                },
                "inGroup" => function ($group_key) {
                    return app("PassageService")::inGroup($group_key);
                },
                "inGroups" => function ($group_keys) {
                    return app("PassageService")::inGroups($group_keys);
                },
            ],
        ];
    }

    public function registerNotificationRules()
    {
        return [
            "events" => [],
            "actions" => [],
            "conditions" => [
                \JosephCrowell\Passage\NotifyRules\UserLocationAttributeCondition::class,
            ],
            "presets" => '$/josephcrowell/passage/config/notify_presets.yaml',
        ];
    }

    public static function globalPassageKeys()
    {
        traceLog(
            "Deprecated method \JosephCrowell\Passage\Plugin::globalPassageKeys() called. Use PassageService::passageKeys() instead. See Passage Upgrade Guide."
        );
        //trigger_error("Deprecated method \JosephCrowell\Passage\Plugin::globalPassageKeys() called. Use app('PassageService')::passageKeys() instead.", E_USER_DEPRECATED);
        return app("PassageService")::passageKeys();
    }

    public static function passageKeys()
    {
        traceLog(
            "Deprecated method \JosephCrowell\Passage\Plugin::passageKeys() called. Use PassageService::passageKeys() instead. See Passage Upgrade Guide."
        );
        //trigger_error("Deprecated method \JosephCrowell\Passage\Plugin::passageKeys() called. Use app('PassageService')::passageKeys() instead.", E_USER_DEPRECATED);
        return app("PassageService")::passageKeys();
    }

    public static function hasKeyName($key_name)
    {
        traceLog(
            "Deprecated method \JosephCrowell\Passage\Plugin::hasKeyName() called. Use PassageService::hasKeyName() instead. See Passage Upgrade Guide."
        );
        //trigger_error("Deprecated method \JosephCrowell\Passage\Plugin::hasKeyName() called. Use app('PassageService')::hasKeyName() instead.", E_USER_DEPRECATED);
        $keys = app("PassageService")::passageKeys();
        return in_array($key_name, $keys);
    }

    public static function hasKey($key_id)
    {
        traceLog(
            "Deprecated method \JosephCrowell\Passage\Plugin::hasKey() called. Use PassageService::hasKey() instead. See Passage Upgrade Guide."
        );
        //trigger_error("Deprecated method \JosephCrowell\Passage\Plugin::hasKey() called. Use app('PassageService')::hasKey() instead.", E_USER_DEPRECATED);
        $keys = app("PassageService")::passageKeys();
        return array_key_exists($key_id, $keys);
    }

    public static function passageGroups()
    {
        traceLog(
            "Deprecated method \JosephCrowell\Passage\Plugin::passageGroups() called. Use PassageService::passageGroups() instead. See Passage Upgrade Guide."
        );
        //trigger_error("Deprecated method \JosephCrowell\Passage\Plugin::passageGroups() called. Use app('PassageService')::passageGroups() instead.", E_USER_DEPRECATED);
        return app("PassageService")::passageGroups();
    }

    public static function hasGroupName($group_name)
    {
        traceLog(
            "Deprecated method \JosephCrowell\Passage\Plugin::hasGroupName() called. Use PassageService::hasGroupName() instead. See Passage Upgrade Guide."
        );
        //trigger_error("Deprecated method \JosephCrowell\Passage\Plugin::hasGroupName() called. Use app('PassageService')::hasGroupName() instead.", E_USER_DEPRECATED);
        $groups = app("PassageService")::passageGroups();
        return in_array($group_name, $groups);
    }

    public static function hasGroup($group_code)
    {
        traceLog(
            "Deprecated method \JosephCrowell\Passage\Plugin::hasGroup() called. Use PassageService::hasGroup() instead. See Passage Upgrade Guide."
        );
        //trigger_error("Deprecated method \JosephCrowell\Passage\Plugin::hasGroup() called. Use app('PassageService')::hasGroup() instead.", E_USER_DEPRECATED);
        $groups = app("PassageService")::passageGroups();
        return array_key_exists($group_code, $groups);
    }
}