<?php
namespace App\Calendar;

use App\Calendar\CalendarCell;

class Calendar {

    public $first_day_of_week = 0; //0 - sunday, 1 - monday
    public $current_year = null;
    public $current_month = null;
    public $current_day = null;
    public $show_year_selector = true;
    public $monthin_select_year = 1991;
    public $monthax_select_year = 2045;
    public $show_month_selector = true;

    public function __construct($atts = array()) {
        if (isset($atts['first_day_of_week'])) {
            $this->first_day_of_week = $atts['first_day_of_week'];
        }

        if (!isset($atts['year'])) {
            $this->current_year = date('Y');
        } else {
            $this->current_year = $atts['year'];
        }

        if (!isset($atts['month'])) {
            $this->current_month = date('m');
        } else {
            $this->current_month = $atts['month'];
        }

        if (!isset($atts['day'])) {
            $this->current_day = date('d');
        } else {
            $this->current_day = $atts['day'];
        }
        //***
        if (isset($atts['show_year_selector'])) {
            $this->show_year_selector = $atts['show_year_selector'];
        }

        if (isset($atts['show_month_selector'])) {
            $this->show_month_selector = $atts['show_month_selector'];
        }

        if (isset($atts['min_select_year'])) {
            $this->min_select_year = $atts['min_select_year'];
        }

        if (isset($atts['max_select_year'])) {
            $this->max_select_year = $atts['max_select_year'];
        }
    }

    /*
     * Month calendar drawing
     */

    public function make($data = array(), $year = 0, $month = 0) {
        //***
        if ($month == 0 AND $month == 0) {
            $year = $this->current_year;
            $month = $this->current_month;
        }
        //***
        $data['week_days_names'] = $this->get_week_days_names(true);
        $data['cells'] = $this->generate_calendar_cells($year, $month);
        $data['month_name'] = $this->get_month_name($month);
        $data['year'] = $year;
        $data['month'] = $month;
        $data['events'] = array();//here you can transmit events from database (look CalendarCell::draw($events))

        return $data;
    }

    private function generate_calendar_cells($year, $month) {
        $year = intval($year);
        $month = intval($month);
        //***
        $first_week_day_in_month = date('w', mktime(0, 0, 0, $month, 1, $year)); //from 0 (sunday) to 6 (saturday)
        $days_count = $this->get_days_count_in_month($year, $month);
        $cells = array();
        //***
        if ($this->first_day_of_week == $first_week_day_in_month) {
            for ($d = 1; $d <= $days_count; $d++) {
                $cells[] = new CalendarCell($year, $month, $d);
            }
            //***
            $cal_cells_left = 5 * 7 - $days_count;
            $next_month_data = $this->get_next_month($year, $month);
            for ($d = 1; $d <= $cal_cells_left; $d++) {
                $cells[] = new CalendarCell($next_month_data['y'], $next_month_data['m'], $d, false);
            }
        } else {
            //***
            if ($this->first_day_of_week == 0) {
                $cal_cells_prev = 6 - (7 - $first_week_day_in_month); //checked, is right
            } else {
                if ($first_week_day_in_month == 1) {
                    $cal_cells_prev = 0;
                } else {
                    if ($first_week_day_in_month == 0) {
                        $cal_cells_prev = 6 - 1;
                    } else {
                        $cal_cells_prev = 6 - (7 - $first_week_day_in_month) - 1;
                    }
                }
            }
            //***
            $prev_month_data = $this->get_prev_month($year, $month);
            $prev_month_days_count = $this->get_days_count_in_month($prev_month_data['y'], $prev_month_data['m']);

            for ($d = $prev_month_days_count - $cal_cells_prev; $d <= $prev_month_days_count; $d++) {
                $cells[] = new CalendarCell($prev_month_data['y'], $prev_month_data['m'], $d, false);
            }

            //***
            for ($d = 1; $d <= $days_count; $d++) {
                $cells[] = new CalendarCell($year, $month, $d);
            }
            //***
            //35(7*5) or 42(7*6) cells
            $busy_cells = $cal_cells_prev + $days_count;
            $cal_cells_left = 0;
            if ($busy_cells < 35) {
                $cal_cells_left = 35 - $busy_cells - 1;
            } else {
                $cal_cells_left = 42 - $busy_cells - 1;
            }
            //***
            if ($cal_cells_left > 0) {
                $next_month_data = $this->get_next_month($year, $month);
                for ($d = 1; $d <= $cal_cells_left; $d++) {
                    $cells[] = new CalendarCell($next_month_data['y'], $next_month_data['m'], $d, false);
                }
            }
        }
        //***
        return $cells;
    }

    public function get_next_month($month, $year) {
        $year = intval($year);
        $month = intval($month);

        //***
        $month++;
        if ($month % 13 == 0 OR $month > 12) {
            $year++;
            $month = 1;
        }

        return array('y' => $year, 'm' => $month);
    }

    public function get_prev_month($month, $year) {
        $year = intval($year);
        $month = intval($month);

        //***
        $month--;
        if ($month <= 0) {
            $year--;
            $month = 12;
        }

        return array('y' => $year, 'm' => $month);
    }

    public function get_days_count_in_month($year, $month) {
        return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
    }

    public static function get_month_name($month) {
        $names = self::get_monthes_names();
        return $names[intval($month)];
    }

    public function get_week_day_name($num, $shortly = false) {
        $names = $this->get_week_days_names($shortly);
        return $names[intval($num)];
    }

    public function get_week_days_names($shortly = false) {
        if ($this->first_day_of_week == 1) {
            if ($shortly) {
                return array(
                    1 => 'Mo',
                    2 => 'Tu',
                    3 => 'We',
                    4 => 'Th',
                    5 => 'Fr',
                    6 => 'Sa',
                    7 => 'Su'
                );
            }

            return array(
                1 => 'Monday',
                2 => 'Tuesday',
                3 => 'Wednesday',
                4 => 'Thursday',
                5 => 'Friday',
                6 => 'Saturday',
                7 => 'Sunday'
            );
        } else {
            if ($shortly) {
                return array(
                    0 => 'Su',
                    1 => 'Mo',
                    2 => 'Tu',
                    3 => 'We',
                    4 => 'Th',
                    5 => 'Fr',
                    6 => 'Sa'
                );
            }

            return array(
                0 => 'Sunday',
                1 => 'Monday',
                2 => 'Tuesday',
                3 => 'Wednesday',
                4 => 'Thursday',
                5 => 'Friday',
                6 => 'Saturday'
            );
        }
    }

    public static function get_monthes_names() {
        return array(
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        );
    }
}
