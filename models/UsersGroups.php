<?php namespace JosephCrowell\Passage\Models;

use Model;

/**
 * UsersGroups Model
 */
class UsersGroups extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = "users_groups";

    /**
     * @var array Guarded fields
     */
    protected $guarded = ["*"];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    public $belongsTo = [
        "user" => [
            "Winter\User\Models\User",
            "key" => "user_id",
            "otherKey" => "id",
        ],

        "group" => [
            "Winter\User\Models\UserGroup",
            "table" => "user_groups",
            "key" => "user_group_id",
            "otherkey" => "id",
        ],
    ];

    public $belongsToMany = [
        "passage_permissions" => [
            "JosephCrowell\Passage\Models\Permission",
            "table" => "josephcrowell_passage_groups_permissions",
            "key" => "user_group_id",
            "otherKey" => "permission_id",
        ],
    ];
}