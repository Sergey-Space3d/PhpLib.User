<?php
// Copyright 2005-2021 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Date Range Selector Form */
class DateRangeSelectorForm extends SelectorForm
{
    /** Initialize form contents */
    protected function init_contents(array $args)
    {
        parent::init_contents($args);
        @extract($args);
        
        $this->add_inner(new CHtmlHidden('show_days', $show_days));
        $this->add_inner(new CHtmlHidden('show_months', $show_months));
        $this->add_inner(new CHtmlHidden('show_years', $show_years));
        $this->add_inner(new CHtmlHidden('timespan', $timespan));
    }
}
?>