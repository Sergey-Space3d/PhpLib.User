<?php
// Copyright 2005-2021 Sergey Shustov.
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
        $arr = array();
        
        $ts = time();
        $year = date('Y', $ts);
        $month = date('n', $ts);
        
        $ts_to = time() + 3600;
        $ts_from = $ts_to - 31 * 86400;
        $range = "{$ts_from}-{$ts_to}";
        $arr[$range] = 'Last month';
        
        for ($m = $month, $m_to = $month - 12; $m > $m_to; $m--)
        {
            $ts_from = mktime(0, 0, 0, $m, 1, $year);
            $ts_to = mktime(0, 0, 0, $m + 1, 1, $year) - 1;
            $range = "{$ts_from}-{$ts_to}";
            $arr[$range] = date('F Y', $ts_from);
        }
        
        for ($y = $year; $y > $year - 3; $y--)
        {
            $ts_from = mktime(0, 0, 0, 1, 1, $y);
            $ts_to = mktime(0, 0, 0, 1, 1, $y + 1) - 1;
            $range = "{$ts_from}-{$ts_to}";
            $arr[$range] = $y;
        }
        
        return $arr;
    }
}
?>