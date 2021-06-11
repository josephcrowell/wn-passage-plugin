<?php namespace JosephCrowell\Passage\Models;

use Model;

/**
 * UserGroupsPermissions Model
 */
class UserGroupsPermissions extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = "josephcrowell_passage_groups_permissions";

    /**
     * @var array Guarded fields
     */
    protected $guarded = [];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ["*"];

    /**
     * @var array Relations
     */
    public $hasOne = [
        "permission" => [
            "JosephCrowell\Passage\Models\Permission",
            "table" => "josephcrowell_passage_permissions",
            "key" => "permission_id",
            "otherKey" => "id",
        ],
        "group" => [
            "Winter\User\Models\UserGroup",
            "table" => "user_groups",
            "key" => "user_group_id",
            "otherKey" => "id",
        ],
    ];
}