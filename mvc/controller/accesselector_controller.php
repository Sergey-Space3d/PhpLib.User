<?php
// Copyright 2005-2021 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Access Ledger Selector Controller */
class AccesSelectorController extends SelectorController
{
    /** The constructor */
    public function __construct($action, array $args = null)
    {
        parent::__construct($action, $args, 'CDbLedger');
    }
    
    /** Returns selection name */
    public function get_sel_name() { return SELECTOR_ACCESS_ID; }
    
    /** Returns array of instances */
    protected function get_items()
    {
        $operator_id = LoggableAccess::Instance()->get_logged()->get_id();
        $is_admin = LoggableAccess::Instance()->is_logged_admin();
        $items = AccessMan::Instance()->get_access($operator_id, $is_admin, true);
        if ($this->get_arg('links_only')) $items = AccessMan::Instance()->filter_links($items, true, false);
        
        return $items;
    }
}
?>