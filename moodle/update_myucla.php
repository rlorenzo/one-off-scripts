<?php
/*
 * Quick and dirty script to mass update all urls at My.UCLA from
 * pilot.ccle.ucla.edu to ccle.ucla.edu
 */


define('CLI_SCRIPT', true);

require(dirname(__FILE__).'/config.php');

// get myucla updater class
require($CFG->dirroot.'/'.$CFG->admin.'/tool/myucla_url/myucla_urlupdater.class.php');

// flags
$UPDATEURLS = true;    // if false, then will display urls to update, else will update
$LIMIT = 0; // num of urls to update. if 0, then will update all

$urlupdater = new myucla_urlupdater();

// get all term/srs courses on system
$courses = $DB->get_recordset('ucla_request_classes');
if (!$courses->valid()) {
    die('empty result from ucla_request_classes');
}

// go through each course and see what the MyUCLA url is
$num_updated = 0;
foreach ($courses as $course) {
    $sending_urls = array();
    $sending_urls[1]['term'] = $course->term;
    $sending_urls[1]['srs'] = $course->srs;

//    echo "Working on " . $course->term . '-' . $course->srs . "\n";
    $result = $urlupdater->send_MyUCLA_urls($sending_urls);

    // check to see if returning url is pilot
    $url = trim($result[1]);
    if (strpos($url, 'pilot.ccle.ucla.edu')) {
        echo "Working on " . $course->term . '-' . $course->srs . "\n";
//        echo "Found pilot url: $url\n";

        // see what we will change url to be
        $new_url = str_replace('pilot.ccle.ucla.edu', 'ccle.ucla.edu', $url);    
        if ($UPDATEURLS) {
            // change url to remove pilot
            $sending_urls[1]['url'] = $new_url;
            $result = $urlupdater->send_MyUCLA_urls($sending_urls, true);

            if (strpos($result[1], myucla_urlupdater::expected_success_message) === false) {
                // error occurred!
                echo(sprintf("ERROR: Cannot update url for %s-%s: result %s\n", $course->term, $course->srs, $result[1]));
                continue;
            } else {
                echo "Updated url: $new_url\n";
            }
        } else {
            echo "Would have updated url: $new_url\n";
        }

        ++$num_updated;

    } else if (empty($url)) {
//        echo "Skipping blank url\n";
    }else {
//        echo "Skipping non-pilot url: $url\n";
    }

    if (!empty($LIMIT) && $num_updated >= $LIMIT) {
        echo "LIMIT reached\n";
        break;
    }
}
$courses->close();

