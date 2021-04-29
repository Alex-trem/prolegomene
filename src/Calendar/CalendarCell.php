<?php 
namespace App\Calendar;

class CalendarCell {

    public $cell_year = null;
    public $cell_month = null;
    public $cell_day = null;
    public $in_current_month = true;

    public function __construct($year, $month, $d, $in_current_month = true) {
        $this->cell_year = $year;
        $this->cell_month = $month;
        $this->cell_day = $d;
        $this->in_current_month = $in_current_month;
    }

    public function get_week_day_num() {
        return date('w', mktime(0, 0, 0, $this->cell_month, $this->cell_day, $this->cell_year)); //from 0 (sunday) to 6 (saturday);
    }

    public function draw($events) {
        $this_day_events = 0;
        if (is_array($events)) {
            if (isset($events[$this->cell_year][$this->cell_month][$this->cell_day])) {
                $this_day_events = count($events[$this->cell_year][$this->cell_month][$this->cell_day]);
            }
        } else {
            $events = array();
        }
        ?>

        <span class="pn_cal_cell_ev_counter" <?php if ($this_day_events <= 0): ?>style="display: none;"<?php endif; ?>><?php echo $this_day_events ?></span>
        <a  data-year="<?php echo $this->cell_year ?>" 
            data-month="<?php echo $this->cell_month ?>" 
            data-day="<?php echo $this->cell_day ?>" 
            data-week-day-num="<?php echo $this->get_week_day_num() ?>" 
            href="javascript:void(0);" 
            class="<?php if ($this->in_current_month): ?>pn_this_month<?php else: ?>other_month<?php endif; ?>">
            <?php echo $this->cell_day ?>
            </a>


        <?php
    }

}