<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Date Range Selector Controller */
class DateRangeSelectorController extends SelectorController
{
    /** The constructor */
    public function __construct($action, array $args = null)
    {
        parent::__construct($action, $args, new CDbLabel('Date Range'));
    }
    
    /** Returns selection name */
    public function get_sel_name() { return SELECTOR_DATE_RANGE; }
    
    /** Returns array of instances */
    protected function get_items()
    {
        $show_days = $this->get_value('show_days');
        $show_months = $this->get_value('show_months');
        $show_years = $this->get_value('show_years');
        $timespan = (int)$this->get_value('timespan');
        
        if (!$show_days && !$show_months && !$show_years)
        {
            // Show months and years default
            $show_months = true;
            $show_years = true;
        }
        
        // Make 3 years default
        if ($timespan <= 0) $timespan = 94608000;
        $ts_min = time() - $timespan;

        $ts = time();
        $year = date('Y', $ts);
        $month = date('n', $ts);
        $day =  date('d', $ts);
        
        $arr = array();
        
        if ($show_days)
        {
            $ts_to = time();
            $ts_from = $ts_to - 86400;
            $range = "{$ts_from}-{$ts_to}";
            $arr[$range] = 'Last 24 hours';
            
            for ($d = $day, $d_to = $day - 7; $d > $d_to; $d--)
            {
                $ts_from = mktime(0, 0, 0, $month, $d, $year);
                if ($ts_min > $ts_from) break;
                $ts_to = mktime(0, 0, 0, $month, $d + 1, $year) - 1;
                $range = "{$ts_from}-{$ts_to}";
                $arr[$range] = date('D M j', $ts_from);
            }
        }
        
        if ($show_months)
        {
            $ts_to = time() + 3600;
            $ts_from = $ts_to - 31 * 86400;
            $range = "{$ts_from}-{$ts_to}";
            $arr[$range] = 'Last month';
            
            for ($m = $month, $m_to = $month - 12; $m > $m_to; $m--)
            {
                $ts_from = mktime(0, 0, 0, $m, 1, $year);
                if ($ts_min > $ts_from) break;
                $ts_to = mktime(0, 0, 0, $m + 1, 1, $year) - 1;
                $range = "{$ts_from}-{$ts_to}";
                $arr[$range] = date('F Y', $ts_from);
            }
        }
        
        if ($show_years)
        {
            for ($y = $year, $years = ceil($timespan / 31536000); $y > $year - $years; $y--)
            {
                $ts_from = mktime(0, 0, 0, 1, 1, $y);
                //if ($ts_min > $ts_from) break;
                $ts_to = mktime(0, 0, 0, 1, 1, $y + 1) - 1;
                $range = "{$ts_from}-{$ts_to}";
                $arr[$range] = $y;
            }
        }
        
        return $arr;
    }
}
?>