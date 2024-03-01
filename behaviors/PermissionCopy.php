<?php
namespace JosephCrowell\Passage\Behaviors;

use Winter\User\Models\UserGroup;

/**
 * Adds features for copying permissions to another group
 *
 * This behavior is implemented in the component like so:
 *
 *    public $implement = ['JosephCrowell.Passage.Behaviors.PermissionCopy'];
 *
 *
 **/

class PermissionCopy extends \Winter\Storm\Extension\ExtensionBase
{
    protected $controller;
    public $allGroups;
    /**
     * Constructor
     */
    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    public function getAllGroups()
    {
        $this->controller->allGroups = UserGroup::orderBy("name")->get();
    }

    public function onCopy()
    {
        $group = UserGroup::find(post("CGid"));
        if (!$group->passage_permissions->count() > 0)
        {
            return [];
        }
        foreach ($group->passage_permissions as $permission)
        {
            $funct_lines[] =
                '$(\'input:checkbox[name="UserGroup[passage_permissions][]"][value="' .
                $permission->id .
                '"]\').prop( "checked", true );';
        }

        return [
            "#copyGpermissions" =>
                '
			<script type="text/javascript">
			               ' .
                implode("", $funct_lines) .
                '
			</script>
			',
        ];
    }

    public function onGetGroups()
    {
        return ["#copyForm" => "fo"];
    }
}
