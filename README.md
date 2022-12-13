# Passage plugin

<p align="center">
  <a href="https://travis-ci.org/josephcrowell/wn-passage-plugin">
    <img src="https://travis-ci.org/josephcrowell/wn-passage-plugin.svg?branch=master">
  </a>
  <a href="https://wintercms.com/plugin/josephcrowell-passage">
    <img src="https://img.shields.io/badge/Winter CMS-Plugin-%23EE7203.svg">
  </a>
  <a href="https://www.patreon.com/josephcrowell">
    <img src="https://img.shields.io/badge/Support_on-Patreon-green.svg">
  </a>
  <a href="https://creativecommons.org/licenses/by-sa/4.0/">
    <img src="https://img.shields.io/badge/License-CC%20BY--SA%204.0-lightgrey.svg">
  </a>
</p>

( Installation code : **josephcrowell.wn-passage-plugin** ) Requires ( **Winter.User** )

This plugin adds a front end user group permission system to [WinterCMS](http://wintercms.com).

Download the plugin to the plugins directory and logout and log in back into Winter backend. Go to the Passage Permissions page via the side menu in users in the backend and add your permissions.

<h3>User Permision / Passage Permission Entry</h3>

In the backend under Users (Winter.Users) you will find a sidemenu item called **"Passage Permissions".** This is where you enter your permission names and an optional description.

In the backend under Users you will find a button at the top called **"User Groups"**. Press button to see groups. When editing a group you will find check boxes at the bottom for each "Passage Permission". This is where you assign permissions for each user group.

<h3>User Overrides</h3>

In the backend under Users (Winter.Users) you will find a sidemenu item called **"User Overrides".**

User overrides allow you to add permissions to individual users. You can also remove permission from users by adding a override and unchecking the **Grant** checkbox.

<h3>User Permisions in Pages or Partials</h3>

On a page you may restrict access to a portion of view by using the following twig functions:

    {% if can('calendar_meetings') %}

    <p>This will show only if the user belongs to a Winter.User Usergroup that includes the permission named "calendar_meetings".</p>

    {% else %}

    <p>This will show if the user DOES NOT belong to a Winter.User Usergroup that include the permission named "calendar_meetings".</p>

    {% endif %}



    {% if inGroup('my_admins') %}

    <p>This will show only if the user belongs to a Winter.User Usergroup that has the code "my_admins".</p>

    {% else %}

    <p>This will show if the user DOES NOT belong to a Winter.User Usergroup that has the code "my_admins".</p>

    {% endif %}


    <p>This will show for all users regardless of permissions.</p>


    {% if inGroupName('My Admins') %}

    <p>This will show only if the user belongs to a Winter.User Usergroup that is named "My Admins".</p>

    {% else %}

    <p>This will show if the user DOES NOT belong to a Winter.User Usergroup that is named "My Admins".</p>

    {% endif %}


    <p>This will show for all users regardless of permissions.</p>

<h2>Available Twig Functions</h2>

- can('PermissionName') - Check a passage permission name
- hasPermissionName('PermissionName') - Check a passage permission name
- hasPermissionNames(['PermissionName1','PermissionName2','PermissionName3']) - Check an array of passage permission names
- hasPermission(PermissionId) (where PermissionId is an integer) - Check a passage permission id
- hasPermissions([PermissionId1,PermissionId2,PermissionId3]) - Check an array of passage permission ids

- inGroupName('GroupName') - Check a passage group name
- inGroupNames(['Group Name','Group Name 2','Group Name 3']) - Check an array of passage group names
- inGroup('GroupCode') - Check a passage group code
- inGroups(['GroupCode1','GroupCode2','GroupCode3']) - Check an array of passage group codes

<h3>User Permisions in Your Own Plugins</h3>

    // Passage Service Methods can be accessed in one of two ways:
    $permissions_by_name = PassageService::passagePermissions(); // by Alias
    //OR
    $permissions_by_name = app('PassageService')::passagePermissions(); // by App Service

    // Get all permissions for the user in an array
    $permissions_by_name = PassageService::passagePermissions();

    /**
    * OR
    *
    * In your plugin you may restrict access to a portion of code:
    **/

    // check for permission directly using hasPermissionName( $permission_name )
    $permissionGranted = PassageService::hasPermissionName('view_magic_dragon');
    if($permissionGranted) {
     // Do stuff
    }

    /**
    * OR
    *
    *  Lets say you have a model that uses a permission field containg the id of a
    *   permission permission and want to see if model permission matches.
    *
    *  Example:
    *  $model->perm_id = 5 which came from a dropdown that contained permissions
    *  from PassageService::passagePermissions();
    **/

    $model = Model::first();
    // check for permission directly using hasPermission( $permission_id )
    if(PassageService::hasPermission($model->perm_id)) {
        // Do Stuff
    }else{
        // Do other Stuff if user does NOT have permission
    }

    /**
    * OR
    *
    *  Get Array of Groups
    **/

    // You can get array of the users groups keyed by the code of the group
    $groups = PassageService::passageGroups()

    /**
    * OR
    *
    *  Check group membership by group code
    **/

    // use hasGroup($group_code) to check membership
    $isCool = PassageService::hasGroup('cool_people')

    /**
    * OR
    *
    *  Check group membership by group Name
    *   Note: Group names are not guaranteed to be unique.
    *   DO NOT CHECK BY GROUP NAME if security is an issue.
    **/

    // use hasGroupName($group_name) to check membership
    $isInGroupNamedCool = PassageService::hasGroupName('Cool')

<h2>Available Passage Service Methods</h2>

- passagePermissions() - Get an array of all approved passage permissions for the user
- can($permission_name) - (alias of hasPermissionName())
- hasPermissionName($permission_name) - Check a passage permission name
- hasPermission(integer $permission_id) - Check a passage permission id
- hasPermissions(array $check_permission_ids) - Check an array of passage permission ids
- hasPermissionNames(array $check_permissions) - Check an array of passage permission names
- passageGroups() - Get an array of all approved passage groups for the user
- inGroupName($group_name) - Check a passage group name
- hasGroupName($group_name) - (alias of inGroupName())
- inGroup($group_code) - Check a passage group code
- hasGroup($group_code) - (alias of inGroup())
- inGroups(array $check_group_codes) - Check an array of passage group ids
- inGroupNames(array $check_groups) - Check an array of passage group names
