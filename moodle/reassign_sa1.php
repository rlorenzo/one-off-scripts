<?php
/*
 * Temp script to reassign all users in sa_1 role to the new 
 * instructional_assistant role.
 */

define('CLI_SCRIPT', true);

require(dirname(__FILE__) . '/config.php');

// get roleid for sa_1 and instructional_assistant
$sa_1 = $DB->get_record('role', array('shortname' => 'sa_1'), '*', MUST_EXIST);
$instructional_assistant = $DB->get_record('role', 
        array('shortname' => 'instructional_assistant'), '*', MUST_EXIST);

// now get all users in sa_1 role and their context
$replace_roles = $DB->get_records('role_assignments', array('roleid' => $sa_1->id));

if (empty($replace_roles)) {
    die("NO ROLES ASSIGNMENTS TO REPLACE\n");
}

// replace role assignments
foreach ($replace_roles as $old_role) {
    role_assign($instructional_assistant->id, $old_role->userid, $old_role->contextid);
    role_unassign($old_role->roleid, $old_role->userid, $old_role->contextid);
    echo sprintf("Replacing role for user %d in context %d\n", $old_role->userid, $old_role->contextid);
}

echo "DONE!\n";