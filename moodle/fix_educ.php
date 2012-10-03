<?php
/*
 * SELECT *  FROM `mdl_context` WHERE `path` LIKE '/1/4862/4863%' and contextlevel=50
 * 
 */
define('CLI_SCRIPT', 1);
require_once('config.php');
require_once($CFG->dirroot . '/course/lib.php');

$sql = "SELECT  *  
        FROM    {context} 
        WHERE   path LIKE '/1/4862/4863%' AND 
                contextlevel=50";

$results = $DB->get_records_sql($sql);

echo 'found ' . count($results) . ' records';

// build array of courseids to move
$courses = array();
foreach ($results as $result) {
    $courses[] = $result->instanceid;
} 


move_courses($courses, 66);
echo 'Done!';