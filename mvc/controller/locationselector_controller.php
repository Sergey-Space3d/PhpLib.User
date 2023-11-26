<?php
// Copyright 2005-2021 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Location Selector Controller */
class LocationSelectorController extends SelectorController
{
    /** The constructor */
    public function __construct($action, array $args = null)
    {
        parent::__construct($action, $args, 'Location');
    }
    
    /** Returns selection name */
    public function get_sel_name() { return SELECTOR_LOCATION_ID; }
    
    /** Returns array of instances */
    protected function get_items()
    {
        $id_owner = (int)CHtmlForm::get_value(SELECTOR_OWNER_ID);
        return $id_owner ? LoggableAccess::Instance()->get_locations($id_owner) : null;
    }
    
    /** Inquire if "all" selection is available */
    protected function can_all() { return LoggableAccess::Instance()->can(OperatorAccess::ACCESS_ALL_LOCATIONS); }
    
    /** Called after the item is selected */
    protected function on_selected()
    {
        CHtmlForm::set_value(SELECTOR_ZONE_ID, null, true);
    }
}
?>