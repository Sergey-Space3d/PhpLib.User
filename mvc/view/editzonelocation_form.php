<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Edit Zone Location Form */
class EditZoneLocationForm extends CForm
{
    /** Initialize form contents */
    protected function init_contents(array $args)
    {
        @extract($args);
        
        $this->add_inner(new CHtmlHidden('id', $id));
        
        $table = new CHtmlTable(array('cellpadding'=>'10', 'class'=>'form', 'style'=>'background-color:white;'));
        $this->add_inner($table);
        
        $table->set_column_attrs(0, array('align'=>'center'));
        
        if (!$name) $name = Zone::label();
        $style = 'color:white;background-color:grey;width:20px;height:20px;float:right;cursor:pointer;';
        $title = "<b>Assign Locations to {$name}</b><div style='{$style}'>x</div>";
        $this->hotspot = $table->add_row($title, array('style'=>'border-bottom:2px solid #CDA000;'));
        
        $ctrl = new AssignLocationsCtrl($args);
        $table->add_row($ctrl);
        
        $ctrl = new CHtmlSubmit('Submit', array('style'=>'width:120px;'));
        $ctrl->disable_on_click();
        $table->add_row($ctrl);
    }
}
?>