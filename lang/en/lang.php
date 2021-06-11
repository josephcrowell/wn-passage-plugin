<?php
return [
    "plugin" => [
        //Plugin File
        "name" => "Passage",
        "description" => "Permission system for front-end user groups.",
        "backend_menu" => "Passage Permissions",
        "backend_override" => "User Overrides",
        "field_tab" => "Passage Permissions",
        "field_label" => "Passage Permissions",
        "field_commentAbove" => "Check all permissions (permissions) that you want this group to have.",
        "field_emptyOption" => "There are no passage permissions, you should create some!",
        "permiss_label" => "Manage permission names for front-end user-group permissions.",
        "permiss_label_ug" => "Manage front-end user-group permissions.",
    ],
    "permissions_comp" => [
        //Controller
        "page_title" => "Manage Passage Permissions",
        "new" => "New Permission",
        "permissions" => "Permissions",
        "return" => "Return to permissions list",
        "saving" => "Saving Permission...",
        "deleting" => "Deleting Permission...",
        "delete_confirm" => "Are you sure you want to delete this permission?",
        "delete_selected_confirm" => "Are you sure you want to delete the selected Permissions?",
    ],
    "permission" => [
        // Model
        "model" => "Permission",
        "models" => "Permissions",
        "id" => "ID",
        "name" => "Name",
        "description" => "Description",
        "updated" => "Updated",
        "created" => "Created",
        "people" => "People associated by allowed groups ( Overrides not considered )",
        "groups" => "Groups having this permission",
    ],

    "overrides_comp" => [
        //Controller
        "page_title" => "Manage Passage Overrides",
        "new" => "New Override",
        "overrides" => "Overrides",
        "return" => "Return to override list",
        "saving" => "Saving Override...",
        "deleting" => "Deleting Override...",
        "delete_confirm" => "Are you sure you want to delete this override?",
        "delete_selected_confirm" => "Are you sure you want to delete the selected Overrides?",
        "delete_selected" => "Delete Selected",
    ],
    "override" => [
        // Model
        "model" => "Override",
        "models" => "Overrides",
        "user_id" => "User",
        "permission_id" => "Passage Permission",
        "grant" => "Grant",
        "description" => "Description / Note",
        "updated" => "Updated",
        "created" => "Created",

        "error_duplicate" => "Duplicate override. Locate and edit existing override instead of creating duplicate.",
    ],
    "choose_one" => "-- Choose One --",
    "copy" => "Copy From Another Group",
    "copy_comment" => "Click group name to copy all permissions from that group",
];