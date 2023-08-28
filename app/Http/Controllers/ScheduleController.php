<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use stdClass;

class ScheduleController extends BaseController
{
    public function init()
    {
        //init subject table
        // DB::table('subject')->delete();
        // $schedule_distinct_subject = DB::table('schedule')
        //             ->selectRaw('LEFT(class_code, 6) as subject_code, subject_name')
        //             ->distinct()->get();
        // // dd($schedule_distinct_subject[0]->subject_code);
        // $schedule = DB::table('schedule')->selectRaw('id, LEFT(class_code, 6) as subject_code, subject_name')->get();
        // foreach ($schedule_distinct_subject as $key => $value) {
        //     $subject_name = $value->subject_name;
        //     $subject_code = $value->subject_code;
        //     DB::table('subject')->insert([
        //         'subject_code' => $subject_code,
        //         'subject_name' => $subject_name
        //     ]);
        //     foreach ($schedule as $item) {
        //         if (strcmp($item->subject_code, $value->subject_code) == 0) {
        //             DB::table('schedule')->where('id', $item->id)->update([
        //                 'subject_code' => $subject_code
        //             ]);
        //         }
        //     }
        // }

        //init teacher table
        // $schedule_distinct_teacher = DB::table('schedule')
        //             ->select('teacher')
        //             ->distinct()->get();
        // $schedule = DB::table('schedule')->get();
        // foreach ($schedule_distinct_teacher as $key => $value) {
        //     $teacher_name = $value->teacher;
        //     DB::table('teacher')->updateOrInsert(
        //         ['teacher_name' => $teacher_name],
        //         ['teacher_name' => $teacher_name]
        //     );
        //     foreach ($schedule as $item) {
        //         if (strcmp($item->teacher, $value->teacher) == 0) {
        //             DB::table('schedule')->where('id', $item->id)->update([
        //                 'teacher_id' => $key+1
        //             ]);
        //         }
        //     }
        // }
    }

    public function index()
    {
        $data = DB::table('schedule')
            ->orderBy('subject_code')
            ->orderBy('teacher_id')
            ->get();

        return view('schedule.index', compact('data'));
    }

    public function merge()
    {
        $data = DB::table('schedule')
            ->join('subject', 'schedule.subject_code', '=', 'subject.subject_code')
            ->get();

        $distinct = DB::table('schedule')
            ->selectRaw('subject_code, teacher_id, COUNT(*) as possibilities')
            ->groupBy('subject_code', 'teacher_id')
            ->orderBy('subject_code')
            ->orderBy('teacher_id')
            ->get();

        // dd($distinct);
        $this->mergeClass($data, $distinct, 1);

        return view('schedule.index', compact('data'));
    }

    public function detail()
    {
        $data = DB::table('schedule_detail')
            ->orderBy("START_DATE")
            ->orderBy("DAY_STUDY")
            ->orderBy("SESSION")
            ->orderBy("ENROLL_CLASS")
            ->get();
        // dd($data);
        // dd($this->findMergedClass($data->toArray()));

        return view('schedule.detail', [
            'data' => $this->findMergedClass($data->toArray())
        ]);

        // foreach ($data as $key => $schedule) {
        //     // dd($schedule);
        //     $weeks = $this->parseWeekStudy($schedule->WEEK_STUDY);
        //     DB::table('schedule_detail')->where('ID', $schedule->ID)->update([
        //         'WEEKS' => json_encode($weeks)
        //     ]);
        // }
    }

    public function makeClassroom()
    {
        $schedules = DB::table('schedule_detail')->orderBy('CLASS_CODE')->orderBy('WEEK_STUDY')->orderBy('ENROLL_CLASS')->get();

        $classrooms = array();

        for ($i = 0; $i < sizeof($schedules); $i++) {
            $classroom = new stdClass();
            $num = 0;

            $is_included = false;
            foreach ($classrooms as $class) {
                if ($class->CLASS_2nd == $schedules[$i]->ENROLL_CLASS && $class->WEEKS == $schedules[$i]->WEEKS) {
                    $is_included = true;
                    break;
                }
            }
            if ($is_included)
                continue;

            $is_merged = false;
            for ($j = $i + 1; $j < sizeof($schedules); $j++) {
                if ($schedules[$i]->CLASS_CODE == $schedules[$j]->CLASS_CODE && $schedules[$i]->WEEKS == $schedules[$j]->WEEKS) {
                    $is_merged = true;
                    $classroom->CLASS_CODE = $schedules[$i]->CLASS_CODE;
                    $classroom->CLASS_1st = $schedules[$i]->ENROLL_CLASS;
                    $classroom->CLASS_2nd = $schedules[$j]->ENROLL_CLASS;
                    $classroom->SESSION = $schedules[$i]->SESSION;
                    $classroom->DAY_OF_WEEK = $schedules[$i]->DAY_STUDY;
                    $classroom->WEEKS = $schedules[$i]->WEEKS;

                    array_push($classrooms, $classroom);
                    break;
                }
            }
            if ($is_merged)
                continue;

            $classroom->CLASS_CODE = $schedules[$i]->CLASS_CODE;
            $classroom->CLASS_1st = $schedules[$i]->ENROLL_CLASS;
            $classroom->CLASS_2nd = null;
            $classroom->SESSION = $schedules[$i]->SESSION;
            $classroom->DAY_OF_WEEK = $schedules[$i]->DAY_STUDY;
            $classroom->WEEKS = $schedules[$i]->WEEKS;

            array_push($classrooms, $classroom);
        }

        // DB::table('classes')->delete();
        // foreach ($classrooms as $classroom) {
        //     foreach (json_decode($classroom->WEEKS) as $week) {
        //         DB::table('classes')->insert([
        //             'class_code' => $classroom->CLASS_CODE,
        //             'class_1st' => $classroom->CLASS_1st,
        //             'class_2nd' => $classroom->CLASS_2nd,
        //             'session' => $classroom->SESSION,
        //             'day_of_week' => $classroom->DAY_OF_WEEK,
        //             'week' => $week
        //         ]);
        //     }
        // }

        //Arange teacher_id for single teacher-subject
        $single_teacher = array();
        // DB::table('classes')->update([
        //     'teacher_id' => null
        // ]);
        $alphas = DB::table('alpha')->get();
        foreach ($alphas as $alpha) {
            if ($alpha->alpha == 10) {
                array_push($single_teacher, $alpha->subject_code);
                // DB::table('classes')->where('class_code', (string)$alpha->subject_code)
                //                     ->update([
                //                         'teacher_id' => $alpha->teacher_id
                //                     ]);
            }
        }

        //Arrange class for the others based on the alpha index
        DB::table('classes')->whereNotIn('class_code', $single_teacher)->update(['teacher_id' => null]);
        $classes = DB::table('classes')->where('teacher_id', null)->orderByRaw('class_code ASC, SUBSTRING("class_1st",1,2) DESC')->get();

        $class_subject_list = array();
        $current_subject = null;
        $current_subject_index = -1;
        foreach ($classes as $class) {
            if ($class->class_code != $current_subject) {
                $current_subject_index++;
                $current_subject = $class->class_code;
                $class_subject = new stdClass();
                $class_subject->classes = [$class];
                $class_subject->alphas = DB::table('alpha')->where('subject_code', $class->class_code)->get();
                array_push($class_subject_list, $class_subject);
            } else {
                array_push($class_subject_list[$current_subject_index]->classes, $class);
            }
        }
        // dd($class_subject_list);

        foreach ($class_subject_list as $class_subject) {
            $sum_alpha = 0;
            foreach ($class_subject->alphas as $alpha) {
                $sum_alpha += $alpha->alpha;
            }

            $group_class = array();
            foreach ($class_subject->classes as $class) {
                if (!in_array($class->class_1st, $group_class))
                    array_push($group_class, $class->class_1st);
                if (!in_array($class->class_2nd, $group_class))
                    array_push($group_class, $class->class_2nd);
            }

            // dd($sum_alpha, sizeof($group_class));

            $normalized_alphas = array();
            $sum_normalized_alpha = 0;
            foreach ($class_subject->alphas as $alpha) {
                $normalize_teacher_alpha = $alpha;
                $normalize_teacher_alpha->alpha = round($alpha->alpha / $sum_alpha * sizeof($group_class));
                $sum_normalized_alpha += $normalize_teacher_alpha->alpha;
                $normalize_teacher_alpha->sub_alpha = 0;
                array_push($normalized_alphas, $normalize_teacher_alpha);
            }

            $diff_normalized_alpha = $diff_normalized_alpha_cache = sizeof($group_class) - $sum_normalized_alpha;
            //Reduce difference from total normalized alphas with total number of classes of current subject
            while ($diff_normalized_alpha > 0) {
                $max_alpha = 0;
                $max_alpha_pos = 0;
                foreach ($normalized_alphas as $normalized_alpha) {
                    $max_alpha = ($normalized_alpha->alpha > $max_alpha) ? $normalized_alpha->alpha : $max_alpha;
                }

                $normalized_alphas[$max_alpha_pos]->alpha--;
                $diff_normalized_alpha--;
            }
            while ($diff_normalized_alpha < 0) {
                $max_alpha = 0;
                $max_alpha_pos = 0;
                foreach ($normalized_alphas as $normalized_alpha) {
                    $max_alpha = ($normalized_alpha->alpha > $max_alpha) ? $normalized_alpha->alpha : $max_alpha;
                }

                $normalized_alphas[$max_alpha_pos]->alpha++;
                $diff_normalized_alpha++;
            }

            dd($normalized_alphas);
            $sub_index = 0;

            //Brute Force algorithm to check if current alphas can be parsed perfectly
            //from diff = 0, is_positive = false and then increase diff by following is_positive from true to false at the end
            $diff = 0;
            $is_positive = false;
            $normalized_alphas = $this->fine_tune_alpha($normalized_alphas, $class_subject->classes, $diff, $is_positive, sizeof($group_class));
            if (sizeof($normalized_alphas) == 0) dd("Can not arrange classes with input alphas");

            foreach ($class_subject->classes as $key_class => $class) {
                if (
                    $key_class >= 1 && $class->class_code == $classes[$key_class - 1]->class_code &&
                    $class->class_1st == $classes[$key_class - 1]->class_1st &&
                    $class->class_2nd == $classes[$key_class - 1]->class_2nd
                ) {
                    $class->teacher_id = $classes[$key_class - 1]->teacher_id;
                    DB::table('classes')
                        ->where('class_code', $class->class_code)
                        ->where('class_1st', $class->class_1st)
                        ->where('class_2nd', $class->class_2nd)
                        ->where('session', $class->session)
                        ->where('day_of_week', $class->day_of_week)
                        ->where('week', $class->week)
                        ->update([
                            'teacher_id' => $class->teacher_id
                        ]);
                } else {
                    // $teacher_alpha = DB::table('alpha')->where('subject_code', $class->class_code)->orderByRaw('alpha DESC, teacher_id ASC')->get();
                    // dd($teacher_alpha);
                    // foreach ($teacher_alpha as $key_teacher => $teacher) {
                    foreach ($normalized_alphas as $key_teacher => $teacher) {
                        $check_duplicated = DB::table('classes')
                            ->where('class_code', $class->class_code)
                            ->where('class_1st', $class->class_1st)
                            ->where('teacher_id', $teacher->teacher_id)
                            ->where('session', $class->session)
                            ->where('day_of_week', $class->day_of_week)
                            ->where('week', $class->week)->get();
                        if (sizeof($check_duplicated) > 0) {
                            if ($key_teacher == sizeof($normalized_alphas) - 1)
                                dd("Find duplicated schedule", $normalized_alphas, $check_duplicated);
                            else
                                continue;
                        } else {
                            $class->teacher_id = $teacher->teacher_id;
                            DB::table('classes')
                                ->where('class_code', $class->class_code)
                                ->where('class_1st', $class->class_1st)
                                ->where('class_2nd', $class->class_2nd)
                                ->where('session', $class->session)
                                ->where('day_of_week', $class->day_of_week)
                                ->where('week', $class->week)
                                ->update([
                                    'teacher_id' => $teacher->teacher_id
                                ]);

                            $teacher->sub_alpha += ($class->class_2nd == null) ? 1 : 2;
                        }
                    }
                }
            }
        }


        dd($classes);
        // dd($classrooms);
    }

    /**
     * 
     * @param array $normalized_alphas
     * @param array $classes
     * @param int $diff
     * @param bool $is_positive
     * @param int $total_classes
     * @param int $current_start_index
     * 
     * @return array
     * 
     */
    public function fine_tune_alpha($normalized_alphas, $classes, $diff, $is_positive, $total_classes, $current_start_index = 0) {
        if ($diff > $total_classes) return [];

        $normalized_alphas_origin = $normalized_alphas;

        foreach ($classes as $key_class => $class) {
            if (
                $key_class >= 1 && $class->class_code == $classes[$key_class - 1]->class_code &&
                $class->class_1st == $classes[$key_class - 1]->class_1st &&
                $class->class_2nd == $classes[$key_class - 1]->class_2nd
            ) {
                continue;
            } else {
                foreach ($normalized_alphas as $key_teacher => $teacher) {
                    $teacher->sub_alpha += ($class->class_2nd == null) ? 1 : 2;
                    if ($teacher->sub_alpha > $teacher->alpha) { 
                        if ($is_positive) return $this->fine_tune_alpha($normalized_alphas_origin, $classes, $diff, false, $total_classes);
                        else return $this->fine_tune_alpha($normalized_alphas_origin, $classes, $diff + 1, true, $total_classes);
                    }
                }
            }
        }


        return $normalized_alphas;
    }

    public function setTeacher()
    {
        DB::table('schedule_detail')->update([
            'teacher_id' => null,
            'teacher_name' => null
        ]);
        DB::table('schedule_new')->update([
            'teacher_id' => null,
            'teacher' => null
        ]);

        $classes = DB::table('classes')
            ->leftJoin('teacher', 'classes.teacher_id', '=', 'teacher.id')
            ->selectRaw('classes.*, teacher.teacher_name')
            ->get();

        $schedule_detail = DB::table('schedule_detail')->get();
        foreach ($schedule_detail as $schedule) {
            foreach ($classes as $class) {
                if ($schedule->CLASS_CODE == $class->class_code && ($schedule->ENROLL_CLASS == $class->class_1st || $schedule->ENROLL_CLASS == $class->class_2nd)) {
                    DB::table('schedule_detail')->where('class_code', $schedule->CLASS_CODE)
                        ->where('ENROLL_CLASS', $schedule->ENROLL_CLASS)
                        ->update([
                            'teacher_id' => $class->teacher_id,
                            'teacher_name' => $class->teacher_name
                        ]);
                    break;
                }
            }
        }

        $schedule_new = DB::table('schedule_new')->get();
        foreach ($schedule_new as $schedule) {
            foreach ($classes as $class) {
                if ($schedule->subject_code == $class->class_code && ($schedule->class_name == $class->class_1st || $schedule->class_name == $class->class_2nd)) {
                    DB::table('schedule_new')->where('subject_code', $schedule->subject_code)
                        ->where('class_name', $schedule->class_name)
                        ->update([
                            'teacher_id' => $class->teacher_id,
                            'teacher' => $class->teacher_name
                        ]);
                    break;
                }
            }
        }

        dd($schedule_detail, $schedule_new);
    }

    public function teacherSubjects()
    {
        $teachers = DB::table('teacher')->get();
        foreach ($teachers as $teacher) {
            $teacher->subjects = DB::table('schedule')
                // ->select('subject_code', 'subject_name')
                ->select('subject_name')
                ->where('teacher_id', $teacher->id)
                ->groupBy('subject_code', 'subject_name')
                ->get()->toArray();
        }

        $subjects = DB::table('subject')->get();
        foreach ($subjects as $subject) {
            $subject->teachers = DB::table('schedule')
                // ->select('teacher_id', 'teacher')
                ->select('teacher')
                ->where('subject_code', $subject->subject_code)
                ->groupBy('teacher_id', 'teacher')
                ->get()->pluck('teacher')->toArray();
            $alphas = DB::table('schedule')
                ->selectRaw('count(*) as total_classes, teacher_id')
                ->where('subject_code', $subject->subject_code)
                ->groupBy('teacher_id')
                ->get()->toArray();


            if (sizeof($alphas) == 1) {
                $alphas[0]->total_classes = 10;
            }

            $subject->alphas = $alphas;
        }

        // dd($teachers, $subjects);
        return view('schedule.teacher_subject', compact('teachers', 'subjects'));
    }

    public function submitAlpha(Request $request)
    {

        $data = DB::table('schedule')
            ->selectRaw('count(*) as TOTAL_CLASSES, teacher_id, teacher')
            ->where('subject_code', '471788')
            ->groupBy('teacher_id', 'teacher')
            ->get();

        // dd($request, $data);

        $subjects = $request->subject;
        $alphas = $request->alpha;
        // dd($subjects, $alphas);

        foreach ($subjects as $key_subject => $subject_code) {
            foreach ($alphas[$key_subject] as $key_alpha => $alpha) {
                DB::table('alpha')->updateOrInsert(
                    [
                        'subject_code' => $subject_code,
                        'teacher_id' => $key_alpha
                    ],
                    [
                        'subject_code' => $subject_code,
                        'teacher_id' => $key_alpha,
                        'alpha' => $alpha
                    ]
                );
            }
        }

        return redirect('/teacher-subject');
    }
}