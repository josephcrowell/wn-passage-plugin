# Passage plugin

( Installation code : __josephcrowell.passage__ ) Requires ( __Winter.User__ )
This plugin adds a front end user group permission system to [WinterCMS](http://wintercms.com).

Download the plugin to the plugins directory and logout and log in back into Winter backend. Go to the Passage Keys page via the side menu in users in the backend and add your permissions/keys.

<h3>User Permision / Passage Key Entry</h3>

In the backend under Users (Winter.Users) you will find a sidemenu item called __"Passage Keys".__  This is where you enter your permission names and an optional description.


In the backend under Users you will find a button at the top called __"User Groups"__. Press button to see groups.  When editing a group you will find check boxes at the bottom for each "Passage Key".  This is where you assign permissions for each user group.

<h3>User Variances</h3>

In the backend under Users (Winter.Users) you will find a sidemenu item called __"User Variances".__

User variances allow you to add permission keys to individual users.  You can also remove permission from users by adding a variance and unchecking the __Grant__ checkbox.

<h3>User Permisions in Pages or Partials</h3>

On a page you may restrict access to a portion of view by using the following twig functions:

    {% if can('calendar_meetings') %}

    <p>This will show only if the user belongs to a Winter.User Usergroup that includes the permision named "calendar_meetings".</p>

    {% else %}

    <p>This will show if the user DOES NOT belong to a Winter.User Usergroup that include the permision named "calendar_meetings".</p>

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

- can('KeyName') - Check a passage key name
- hasKeyName('KeyName') - Check a passage key name
- hasKeyNames(['KeyName1','KeyName2','KeyName3']) - Check an array of passage key names
- hasKey(KeyId) (where KeyId is an integer) - Check a passage key id
- hasKeys([KeyId1,KeyId2,KeyId3]) - Check an array of passage key ids

- inGroupName('GroupName') - Check a passage group name
- inGroupNames(['Group Name','Group Name 2','Group Name 3']) - Check an array of passage group names
- inGroup('GroupCode') - Check a passage group code
- inGroups(['GroupCode1','GroupCode2','GroupCode3']) - Check an array of passage group codes

<h3>User Permisions in Your Own Plugins</h3>

    // Passage Service Methods can be accessed in one of two ways:
    $permission_keys_by_name = PassageService::passageKeys(); // by Alias
    //OR
    $permission_keys_by_name = app('PassageService')::passageKeys(); // by App Service

	// Get all permision keys for the user in an array
	$permission_keys_by_name = app('PassageService')::passageKeys();

	/**
	* OR
	* 
	* In your plugin you may restrict access to a portion of code:
	**/

	// check for permission directly using hasKeyName( $key_name )
	$permissionGranted = app('PassageService')::hasKeyName('view_magic_dragon');
	if($permissionGranted) {
		// Do stuff
	}

	/**
	* OR
	* 
	* 	Lets say you have a model that uses a permission field containg the id of a
	*   permission key and want to see if model permission matches.
	* 
	* 	Example:
	* 	$model->perm_id = 5 which came from a dropdown that contained keys 
	* 	from PassageService::passageKeys();
	**/

	$model = Model::first();
	// check for permission directly using hasKey( $key_id )
	if(PassageService::hasKey($model->perm_id)) {
        // Do Stuff
    }else{
        // Do other Stuff if user does NOT have permission  
    }

	/**
	* OR
	* 
	* 	Get Array of Groups
	**/

	// You can get array of the users groups keyed by the code of the group
	$groups = PassageService::passageGroups()

	/**
	* OR
	* 
	* 	Check group membership by group code
	**/

	// use hasGroup($group_code) to check membership
	$isCool = PassageService::hasGroup('cool_people')

	/**
	* OR
	* 
	* 	Check group membership by group Name
	*   Note: Group names are not guaranteed to be unique.
	*   DO NOT CHECK BY GROUP NAME if security is an issue.
	**/

	// use hasGroupName($group_name) to check membership
	$isInGroupNamedCool = PassageService::hasGroupName('Cool')

<h2>Available Passage Service Methods</h2>

- passageKeys() - Get an array of all approved passage keys for the user
- can($key_name) - (alias of hasKeyName())
- hasKeyName($key_name) - Check a passage key name
- hasKey(integer $key_id) - Check a passage key id
- hasKeys(array $check_key_ids) - Check an array of passage key ids
- hasKeyNames(array $check_keys) - Check an array of passage key names
- passageGroups() - Get an array of all approved passage groups for the user
- inGroupName($group_name) - Check a passage group name
- hasGroupName($group_name) - (alias of inGroupName())
- inGroup($group_code) - Check a passage group code
- hasGroup($group_code) - (alias of inGroup())
- inGroups(array $check_group_codes) - Check an array of passage group ids
- inGroupNames(array $check_groups) - Check an array of passage group names
