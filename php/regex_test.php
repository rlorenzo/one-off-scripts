<?php
/*
 * Quick and dirty regex test script
 */

$pattern = '/^Week [0-9]+$/';

$test_cases = array(
    'Week' => false,
    'Week 1' => true,
    'Week 999' => true,
    'Week 3x2'=> false,
    ' Week 1' => false,
    'Week 1: Intro' => false,
    'week 1' => false,
    'Topic 5' => false,
    'Week 2' => true,
    'Week 3' => true,
    'Week 4' => true,
    'Week 5' => true,
    'Week 6' => true,
    'Week 7' => true,
    'Week 8' => true,
    'Week 9' => true,
    'Week 10' => true,
);


foreach ($test_cases as $test => $expected_outcome) {
    $actual_outcome = false;
    if (preg_match($pattern, $test)) {
        $actual_outcome = true;
    }

    if ($expected_outcome !== $actual_outcome) {
        echo "Test failed on: " . $test  . "\n";
    } else {
	echo "Test success on: " . $test . "\n";
    }
}

