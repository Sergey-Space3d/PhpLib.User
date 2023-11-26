<?php
// Copyright 2005-2021 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Edit Operator Zone Form */
class EditOperatorZoneForm extends CForm
{
    /** Initialize form contents */
    protected function init_contents(array $args)
    {
        @extract($args);
        
        $this->add_inner(new CHtmlHidden('id', $id));
        
        $table = new CHtmlTable(array('cellpadding'=>'10', 'class'=>'form', 'style'=>'background-color:white;'));
        $this->add_inner($table);
        
        $table->set_column_attrs(0, array('align'=>'center'));
        
        $label = Zone::label();
        if (!$name) $name = Operator::label();
        $style = 'color:white;background-color:grey;width:20px;height:20px;float:right;cursor:pointer;';
        $title = "<b>Assign {$label->lower(true)} to {$name}</b><div style='{$style}'>x</div>";
        $this->hotspot = $table->add_row($title, array('style'=>'border-bottom:2px solid #CDA000;'));
        
        if ($all_zones)
        {
            $div = new CHtmlElement('div');
            $ctrl = new CHtmlCheckbox('access_all_locations', 1, $access_all_locations);
            $div->add_inner($ctrl);
            $div->add_inner('&nbsp;Access all locations');
            $table->add_row($div, array('align'=>'left'));
        }
        
        $ctrl = new AssignZonesCtrl($args);
        $table->add_row($ctrl);
        
        $ctrl = new CHtmlSubmit('Submit', array('style'=>'width:120px;'));
        $ctrl->disable_on_click();
        $table->add_row($ctrl);
    }
}
?>