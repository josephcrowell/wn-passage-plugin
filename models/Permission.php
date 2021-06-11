<?php namespace JosephCrowell\Passage\Models;

use Model;

/**
 * Permission Model
 */
class Permission extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = "josephcrowell_passage_permissions";

    /**
     * @var array Guarded fields
     */
    protected $guarded = ["*"];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $belongsToMany = [
        "groups" => [
            "Winter\User\Models\UserGroup",
            "table" => "josephcrowell_passage_groups_permissions",
            "key" => "permission_id",
            "otherkey" => "user_group_id",
        ],
        "users_count" => [
            "Winter\User\Models\UserGroup",
            "table" => "josephcrowell_passage_groups_permissions",
            "count" => true,
        ],
    ];
}