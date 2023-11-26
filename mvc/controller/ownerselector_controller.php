<?php
// Copyright 2005-2021 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Owner Selector Controller */
class OwnerSelectorController extends SelectorController
{
    /** The constructor */
    public function __construct($action, array $args = null)
    {
        parent::__construct($action, $args, 'Owner');
    }
    
    /** Returns selection name */
    public function get_sel_name() { return SELECTOR_OWNER_ID; }
    
    /** Returns array of instances */
    protected function get_items()
    {
        return LoggableAccess::Instance()->get_owners();
    }
    
    /** Inquire if "all" selection is available */
    protected function can_all()
    {
        return LoggableAccess::Instance()->is_logged_admin() ||
        LoggableAccess::Instance()->get_logged()->get_owner_id() == 0;
    }
    
    /** Called after the item is selected */
    protected function on_selected() 
    {
        CHtmlForm::set_value(SELECTOR_LOCATION_ID, null, true);
        CHtmlForm::set_value(SELECTOR_ZONE_ID, null, true);
    }
}
?>