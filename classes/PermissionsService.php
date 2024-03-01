<?php
namespace JosephCrowell\Passage\Classes;

use Auth;
use JosephCrowell\Passage\Models\Permission;
use JosephCrowell\Passage\Models\Override;

/**
 * Passage Service Class
 * Provides methods for checking pemissions of front end users.
 *
 * @package josephcrowell\passage
 * @author Kurt Jensen
 */
class PermissionsService
{
    use \Winter\Storm\Support\Traits\Singleton;

    public static $permissions = null;
    public static $groups = null;

    public function __construct()
    {
    }

    /**
     * Find active user who is logged in
     * @return object Winter\User\Model\User
     */
    public static function getUser()
    {
        if (!($user = Auth::getUser()))
        {
            return false;
        }
        if (!$user->is_activated)
        {
            return false;
        }
        return $user;
    }

    /**
     * Alias of hasPermissionName()
     * @param  string  $permission_name name of Permission
     * @return boolean  true if user has permission
     */
    public static function can($permission_name)
    {
        return self::hasPermissionName($permission_name);
    }

    /**
     * Get an array of all permissions approved for user
     * @return array approved user permissions names keyed by id
     */
    public static function passagePermissions()
    {
        if (self::$permissions === null)
        {
            if (!self::getUser())
            {
                return [];
            }
            $add       = $subtract = [];
            $overrides = Override::where("user_id", self::getUser()->id)->get(["user_id", "permission_id", "grant"]);
            foreach ($overrides as $override)
            {
                if ($override->grant)
                {
                    $add[] = $override->permission_id;
                }
                else
                {
                    $subtract[] = $override->permission_id;
                }
            }

            $query = Permission::whereHas("groups.users", function ($q)
            {
                $q->where("user_id", self::getUser()->id);
            });
            if ($subtract)
            {
                $query->whereNotIn("id", $subtract);
            }
            if ($add)
            {
                $query->orWhereIn("id", $add);
            }
            self::$permissions = $query->lists("name", "id");
        }
        return self::$permissions;
    }

    /**
     * Test if user has a approved permission of a given name
     * @param  string  $permission_name name of Permission
     * @return boolean  true if user has permission
     */
    public static function hasPermissionName(string $permission_name)
    {
        $permissions = self::passagePermissions();
        return in_array($permission_name, $permissions);
    }

    /**
     * Test if user has a approved permission of a given permission id
     * @param  integer  $permission_id id of a Permission
     * @return boolean  true if user has corresponding permission
     */
    public static function hasPermission(int $permission_id)
    {
        $permissions = self::passagePermissions();
        return array_key_exists($permission_id, $permissions);
    }

    /**
     * Test if user has all permissions in a given array approved
     * @param  array  $check_permissions names of Permissions to check
     * @return boolean  true if user has corresponding permissions
     */
    public static function hasPermissions(array $check_permission_ids)
    {
        $permissions = array_flip(self::passagePermissions());
        return count(array_intersect($check_permission_ids, $permissions)) == count($check_permission_ids);
    }

    /**
     * Test if user has all permissions in a given array approved
     * @param  array  $check_permissions names of permissions to check
     * @return boolean  true if user has corresponding permissions
     */
    public static function hasPermissionNames(array $check_permissions)
    {
        $permissions = self::passagePermissions();
        return count(array_intersect($check_permissions, $permissions)) == count($check_permissions);
    }

    /**
     * Group methods
     */

    /**
     * Get an array of all groups approved for user
     * @return array approved user group names keyed by code
     */
    public static function passageGroups()
    {
        if (self::$groups === null)
        {
            if (!($user = self::getUser()))
            {
                return self::$groups = [];
            }
            self::$groups = $user->groups->lists("name", "code");
        }
        return self::$groups;
    }

    /**
     * Test if user is in a group of a given name
     * @param  string  $group_name name of UsersGroup
     * @return boolean  true if user is part of group
     */
    public static function inGroupName(string $group_name)
    {
        if (!($user = self::getUser()))
        {
            return false;
        }
        return in_array($group_name, self::passageGroups());
    }

    /**
     * Alias for inGroupName()
     * @param  string  $group_name name of UsersGroup
     * @return boolean  true if user is part of group
     */
    public static function hasGroupName(string $group_name)
    {
        return self::inGroupName($group_name);
    }

    /**
     * Test if user is in a group of a given user group code
     * @param  string  $group_code code of UsersGroup
     * @return boolean  true if user is part of group
     */
    public static function inGroup(string $group_code)
    {
        return array_key_exists($group_code, self::passageGroups());
    }

    /**
     * Test if user is in a group of a given user group code
     * @param  string  $group_code code of UsersGroup
     * @return boolean  true if user is part of group
     */
    public static function hasGroup(string $group_code)
    {
        return self::inGroup($group_code);
    }

    /**
     * Test if user is in groups in a given array of group codes
     * @param  array  $check_group_codes names of Groups to check
     * @return boolean  true if user is in all groups
     */
    public static function inGroups(array $check_group_codes)
    {
        $group_codes = array_flip(self::passageGroups());
        return count(array_intersect($check_group_codes, $group_codes)) == count($check_group_codes);
    }

    /**
     * Test if user is in groups in a given array of group names
     * @param  array  $check_groups names of Groups to check
     * @return boolean  true if user is in all groups
     */
    public static function inGroupNames(array $check_groups)
    {
        $group_names = self::passageGroups();
        return count(array_intersect($check_groups, $group_names)) == count($check_groups);
    }
}
