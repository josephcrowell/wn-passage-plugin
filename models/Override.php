<?php
namespace JosephCrowell\Passage\Models;

use Lang, Model;
use JosephCrowell\Passage\Models\Permission;
use Winter\Storm\Exception\ValidationException;
use Winter\User\Models\User;

/**
 * Override Model
 */
class Override extends Model
{
    use \Winter\Storm\Database\Traits\Validation;
    /**
     * @var string The database table used by the model.
     */
    public $table = "josephcrowell_passage_overrides";

    public $rules = [
        "permission_id" => "required",
        "user_id" => "required",
    ];

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
    public $belongsTo = [
        "permission" => [
            "JosephCrowell\Passage\Models\Permission",
            "table" => "josephcrowell_passage_permissions",
            "key" => "permission_id",
            "otherkey" => "id",
        ],
        "user" => ["Winter\User\Models\User", "table" => "users", "key" => "user_id", "otherkey" => "id"],
    ];

    public function __construct(array $attributes = [])
    {
        $this->setRawAttributes(
            [
                "grant" => true,
            ],
            true
        );

        parent::__construct($attributes);
    }

    public function beforeValidate()
    {
        $invalid =
            $this->newQuery()
                ->where("id", "!=", $this->id)
                ->where("permission_id", $this->permission_id)
                ->where("user_id", $this->user_id)
                ->count() > 0;
        if ($invalid) {
            throw new ValidationException([
                "unique_attribute" => Lang::get("josephcrowell.passage::lang.override.error_duplicate"),
            ]);
        }
    }

    public function getUserIdOptions()
    {
        $options[0] = Lang::get("josephcrowell.passage::lang.choose_one");
        $users = User::orderBy("surname")
            ->orderBy("name")
            ->get(["surname", "name", "email", "id"]);
        foreach ($users as $user) {
            $options[$user->id] = $user->surname . ", " . $user->name . " - " . $user->email;
        }
        return $options;
    }

    public function getPermissionIdOptions()
    {
        return Permission::lists("name", "id");
    }
}
