<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use stdClass;

class BaseController extends Controller
{
    /**
     * $data    : set of data collection for each teacher_id and subject_code key pairs
     * $num     : number of merging class
     * $result  : recursion array for each teacher_id and subject_code key pairs
     */
    public function mergeSolution($data, $num, $result = array()) {
        echo $num.'-'.intval(sizeof($data)/2).PHP_EOL;
        $solution = array();
        $collection = array_values($data);

        if (intval(sizeof($data)/2) == 0) {
            $sol = new stdClass();
            $sol->subject_code = $collection[0]->subject_code;
            $sol->teacher_id = $collection[0]->teacher_id;
            $sol->credit = $collection[0]->credit;
            $sol->first_class = $collection[0]->class_name;
            $sol->second_class = null;
            $sol->total = $collection[0]->total;
            $sol->begin = $collection[0]->begin;
            $sol->end = $collection[0]->end;
            array_push($solution, $sol);
            array_push($result, $solution);
            return $result;
        }
        if ($num > intval(sizeof($data)/2)) {
            // dd($result);
            // echo (json_encode($result));
            return $result;
        }

        for ($i=0; $i<$num; $i++) {
            $sol = new stdClass();
            $sol->subject_code = $collection[2*$i]->subject_code;
            $sol->teacher_id = $collection[2*$i]->teacher_id;
            $sol->credit = $collection[2*$i]->credit;
            $sol->first_class = $collection[2*$i]->class_name;
            $sol->second_class = $collection[2*$i+1]->class_name;
            $sol->total = $collection[2*$i]->total + $collection[2*$i+1]->total;
            $sol->begin = $collection[2*$i]->begin;
            $sol->end = $collection[2*$i]->end;
            array_push($solution, $sol);
        }

        for ($i=2*$num; $i<sizeof($data); $i++) {
            $sol = new stdClass();
            $sol->subject_code = $collection[$i]->subject_code;
            $sol->teacher_id = $collection[$i]->teacher_id;
            $sol->credit = $collection[$i]->credit;
            $sol->first_class = $collection[$i]->class_name;
            $sol->second_class = null;
            $sol->total = $collection[$i]->total;
            $sol->begin = $collection[$i]->begin;
            $sol->end = $collection[$i]->end;
            array_push($solution, $sol);
        }
        
        // dd(is_array($result));
        array_push($result, $solution);
        // dd($solution, sizeof($data));
        return $this->mergeSolution($data, $num+1, $result);
        // dd($result);
    }
    public function mergeClass($data, $distinct, $min) {
        $result = array();

        // $conditions = array();
        // foreach ($distinct as $value) {
        //     $condition = new stdClass();
        //     $condition->subject_code = $value->subject_code; //identifier
        //     $condition->teacher_id = $value->teacher_id; //identifier
        //     $condition->min = $min; //minimum pairs
        //     $condition->max = intval($value->possibilities/2); //total possibilities
        //     array_push($conditions, $condition);
        // }

        // dd($conditions);
        $keys = ['teacher_id', 'subject_code'];

        $splitArrays = [];

        foreach ($data as $object) {
            // dd(is_object($object));
            $keyValues = array_intersect_key(get_object_vars($object), array_flip($keys)); // get the key values for this object
            $key = json_encode($keyValues); // encode the key values as a JSON string
            
            if (!isset($splitArrays[$key])) {
                $splitArrays[$key] = []; // create a new array for this key if it doesn't exist
            }
            
            $splitArrays[$key][] = $object; // add the object to the array for this key
        }

        // dd($splitArrays);
        // dd($splitArrays['{"teacher_id":"4","subject_code":"471792"}']);
        foreach ($splitArrays as $collection) {
            array_push($result, $this->mergeSolution($collection, 1));
        }

        dd($result);
        // dd(array_reduce($result, 'mergeArrays', array(array())));

        // $this->mergeSolution($splitArrays['{"teacher_id":"4","subject_code":"471792"}'], $conditions, 1);
        $result1 = $this->get_combinations_from_array($result);
        dd($result1);

        return $result;
    }
    function mergeArrays($result, $array) {
        $merged = array();
        foreach ($result as $resultItem) {
            foreach ($array as $arrayItem) {
                $merged[] = array_merge($resultItem, array($arrayItem));
            }
        }
        return $merged;
    }

    function generateAllSchedule1($begin, $end) {
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
        
        // print_r(json_encode($schedule_time_list));
    }

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
                        array_push($schedule_time_list, "Mon ".date('Y-m-d', $newTimestamp));
                    }
                    break;
                case 'Tuesday':
                    for ($j=0; $j<5; $j++) {
                        array_push($schedule_time_list, "Tue ".date('Y-m-d', $newTimestamp));
                    }
                    break;
                case 'Wednesday':
                    for ($j=0; $j<5; $j++) {
                        array_push($schedule_time_list, "Wed ".date('Y-m-d', $newTimestamp));
                    }
                    break;
                case 'Thursday':
                    for ($j=0; $j<5; $j++) {
                        array_push($schedule_time_list, "Thu ".date('Y-m-d', $newTimestamp));
                    }
                    break;
                case 'Friday':
                    for ($j=0; $j<5; $j++) {
                        array_push($schedule_time_list, "Fri ".date('Y-m-d', $newTimestamp));
                    }
                    break;
                case 'Saturday':
                    for ($j=0; $j<5; $j++) {
                        array_push($schedule_time_list, "Sat ".date('Y-m-d', $newTimestamp));
                    }
                    break;
                default:
                    break;
            }
    
        }
        
        // print_r(json_encode($schedule_time_list));
    }

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
                    $new_combination = $this->sort_child_arrays($new_combination);
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
            dd($child);
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

    function parseWeekStudy($week_study) {
        //1-29 weeks

        $weeks = array();
        for ($i=0; $i<strlen($week_study); $i++) {
            if ($week_study[$i] != " ") {
                array_push($weeks, intval(($i+1)/10)*10+intval($week_study[$i]));
            }
        }
        // dd(json_encode($weeks));
        return $weeks;
    }

    public function findMergedClass($data) {
        $collection = array();
        for($i=0; $i<sizeof($data); $i++) {
            if ($data[$i]->START_DATE != $data[$i+1]->START_DATE || $data[$i]->END_DATE != $data[$i+1]->END_DATE) {
                array_push($collection, $data[$i]);
            } else if ($data[$i]->WEEKS == $data[$i+1]->WEEKS) {
                $data[$i]->MERGED_CLASS = $data[$i+1]->ENROLL_CLASS;
                array_push($collection, $data[$i]);
                $i++;
            }
        }

        return $collection;
    }
}