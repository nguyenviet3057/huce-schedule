<?php

function get_combinations_from_array($array_of_arrays) {
    $args = $array_of_arrays;
    $num_args = count($args);

    $combinations = array(array());
    for ($i = 0; $i < $num_args; $i++) {
        $child_arrays = $args[$i];
        $new_combinations = array();
        foreach ($combinations as $combination) {
            foreach ($child_arrays as $child) {
                $new_combination = array_merge($combination, array($child));
                $new_combination = sort_child_arrays($new_combination);
                if (!is_null($new_combination)) {
                    $new_combinations[] = $new_combination;
                }
            }
        }
        $combinations = $new_combinations;
    }

    // Filter out duplicate combinations
    $unique_combinations = array_map('unserialize', array_unique(array_map('serialize', $combinations)));

    return $unique_combinations;
}

function sort_child_arrays($combination) {
    $unique_items = array();
    foreach ($combination as &$child) {
        // print_r ($child);
        sort($child);
        if (in_array($child[1], $unique_items)) {
            return null;
        }
        $unique_items[] = $child[1];
    }
    foreach ($combination as &$child) {
        $child[1] = $unique_items[array_search($child[1], $unique_items)];
    }
    return $combination;
}

$array_of_arrays = array(
    array(
        array('a', 'b'),
        array('c', 'd')
    ),
    array(
        array('1', 'd'),
        array('4', '3')
    ),
    array(
        array('X', 'Y'),
        array('Z', 'b')
    )
);

$result = get_combinations_from_array($array_of_arrays);

// print_r (json_encode($result));

function generateAllSchedule($begin, $end) {
    $schedule_time_list = array();
    $timestamp = strtotime($begin);
    $begin_session = new DateTime($begin);
    $end_session = new DateTime($end);
    print_r($begin_session->diff($end_session)->format('%a'));
    for ($i=0; $i<$begin_session->diff($end_session)->format('%a'); $i++) {
        $newTimestamp = strtotime('+'.$i.'day', $timestamp);
        $dayOfWeek = date("l", $newTimestamp);
        switch ($dayOfWeek) {
            case 'Monday':
                for ($j=0; $j<5; $j++) {
                    array_push($schedule_time_list, "Mon S".($j+1)." ".date('Y-m-d', $newTimestamp));
                }
                break;
            case 'Tuesday':
                for ($j=0; $j<5; $j++) {
                    array_push($schedule_time_list, "Tue S".($j+1)." ".date('Y-m-d', $newTimestamp));
                }
                break;
            case 'Wednesday':
                for ($j=0; $j<5; $j++) {
                    array_push($schedule_time_list, "Wed S".($j+1)." ".date('Y-m-d', $newTimestamp));
                }
                break;
            case 'Thursday':
                for ($j=0; $j<5; $j++) {
                    array_push($schedule_time_list, "Thu S".($j+1)." ".date('Y-m-d', $newTimestamp));
                }
                break;
            case 'Friday':
                for ($j=0; $j<5; $j++) {
                    array_push($schedule_time_list, "Fri S".($j+1)." ".date('Y-m-d', $newTimestamp));
                }
                break;
            case 'Saturday':
                for ($j=0; $j<5; $j++) {
                    array_push($schedule_time_list, "Sat S".($j+1)." ".date('Y-m-d', $newTimestamp));
                }
                break;
            default:
                break;
        }

    }
    
    print_r(json_encode($schedule_time_list));
}

generateAllSchedule(date('2023-04-02'), date('2023-06-29'));