<?php namespace JosephCrowell\Passage\Models;

use Model;

/**
 * UserGroupsKeys Model
 */
class UserGroupsKeys extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'josephcrowell_passage_groups_keys';

    /**
     * @var array Guarded fields
     */
    protected $guarded = [];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['*'];

    /**
     * @var array Relations
     */
    public $hasOne = [
        'key' => ['JosephCrowell\Passage\Models\Key',
            'table' => 'josephcrowell_passage_keys',
            'key' => 'key_id',
            'otherkey' => 'id',
        ],
        'group' => ['Winter\User\Models\UserGroup',
            'table' => 'user_groups',
            'key' => 'user_group_id',
            'otherkey' => 'id',
        ],
    ];
}
