<?php
/**
 * create_peer_review_testing_table.php
 *
 * Creates the peer review and testing table for a given set of people.
 */

$developers = array(
    'Danny',
    'Rex',
    'Lillian',
    'Rohan',
    'Tulasi',
    'Su',
    'Ning',
    'Kubilay',
    'Edwin'
);

sort($developers);

$peerreviewers = array();
$testtesters = array();
$stagetesters = array();

foreach ($developers as $developer) {
    // Generate mapping for peer reviewers.
    $peerreviewer = map_developer($developer, $developers, $peerreviewers,
            array(array()));
    if (!is_null($peerreviewer)) {
        $peerreviewers[$developer] = $peerreviewer;
    } else {
        exit('Could not pair peer review for ' . $developer . "\n");
    }

    // Generate mapping for TEST testers.
    $testtester = map_developer($developer, $developers, $testtesters,
            array($peerreviewers));
    if (!is_null($testtester)) {
        $testtesters[$developer] = $testtester;
    } else {
        exit('Could not TEST tester for ' . $developer . "\n");
    }

    // // Generate mapping for STAGE testers.
    // $stagetester = map_developer($developer, $developers, $stagetesters,
    //         array($peerreviewers, $testtesters));
    // if (!is_null($stagetester)) {
    //     $stagetesters[$developer] = $stagetester;
    // } else {
    //     exit('Could not STAGE tester for ' . $developer . "\n");
    // }
}

// Now generate HTML for table.
$htmltable = '<table class="generaltable"><caption>Sprint</caption><thead><tr>' .
        '<th>Developer</th><th>Peer reviewer</th><th>Tester</th>' .
        '</tr></thead></tbody>';
foreach ($developers as $developer) {
    $htmltable .= sprintf('<tr><td>%s</td><td>%s</td><td>%s</td></tr>',
            $developer, $peerreviewers[$developer], $testtesters[$developer]);
}
$htmltable .= '</tbody></table>';
echo $htmltable . "\n";

// Script functions.

/**
 * For a given set of developers, try to return someone who isn't already
 * assigned.
 *
 * @param string $developer         Developer we are getting a match for.
 * @param array $developers
 * @param array $givenmatchings     The current matching we are doing.
 * @param array $existingmappings   An array of existing mappings.
 *
 * @return null|string              Returns null if match couldn't be found,
 *                                  else returns the name of the developer to
 *                                  pair with given developer.
 */
function map_developer ($developer, array $developers, array $givenmatchings,
        array $existingmappingsarray) {
    // Give up after 100,000 tries, because no mapping might be possible.
    $maxtries = 100000;
    $max = count($developers) - 1;

    $numtries = 0;
    $retval = null;
    while (empty($retval)) {
        ++$numtries;
        if ($numtries > $maxtries) {
            break;
        }

        $possibledeveloper = $developers[rand(0, $max)];

        // Make sure that we aren't trying to assign a developer to themself.
        if ($possibledeveloper === $developer) {
            continue;
        }

        $alreadymatched = false;
        foreach ($existingmappingsarray as $existingmapping) {
            // Make sure that matched developer isn't already assigned for the
            // given assignment level.
            if (in_array($possibledeveloper, $givenmatchings)) {
                $alreadymatched = true;
                break;
            }

            // Check that candidate developer isn't already assigned to the given
            // developer at another assignment level.
            if (isset($existingmapping[$developer]) &&
                    $existingmapping[$developer] === $possibledeveloper) {
                $alreadymatched = true;
                break;
            }
        }

        if (!$alreadymatched) {
            $retval = $possibledeveloper;
            break;
        }
    }

    return $retval;
}
