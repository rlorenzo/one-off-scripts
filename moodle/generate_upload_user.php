<?php
/** 
 * Generate a file that can be used in Moodle's upload user mechanism to bulk
 * add users from a list of UIDs.
 * 
 * Output file needs to be formatted as:
 * username,firstname,lastname,email,idnumber,course1,role1,auth
 * 
 * When uploading users, be sure to select option: Add new and update existing
 */

define('CLI_SCRIPT', true);
require_once('config.php');

// Script variables.
$inputfile = 'public_affairs_uids.txt';
$outputfile = 'public_affairs.txt';
$missingusersfile = 'missingusers.txt';
$courseshortname = 'Graphics and Infographics Training';
$roleshortname = 'projectparticipant';

// Open files.
$output = fopen($outputfile, "w");
$missingusers = fopen($missingusersfile, "w");

// Load list of UIDs into array and make it unique.
$uids = file($inputfile);
$uids = array_unique($uids);

// First line in output file needs to be file columns.
fwrite($output, "username,firstname,lastname,email,idnumber,course1,role1,auth\n");

// Go through each entry and get the user record, if any.
foreach ($uids as $uid) {
    $uid = trim($uid);
    $user = $DB->get_record('user', array('idnumber' => $uid));
    if (!empty($user)) {
        $entry = sprintf("%s,%s,%s,%s,%s,%s,%s,%s\n", $user->username, 
                $user->firstname, $user->lastname, $user->email, 
                $user->idnumber, $courseshortname, $roleshortname, 
                'shibboleth');
        fwrite($output, $entry);
    } else {
        fwrite($missingusers, $uid . "\n");
    }
}

// Close files.
fclose($output);
fclose($missingusers);

echo "DONE!";