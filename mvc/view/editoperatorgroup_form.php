<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Edit Operator Group Form */
class EditOperatorGroupForm extends CForm
{
    /** Initialize form contents */
    protected function init_contents(array $args)
    {
        @extract($args);
        $is_new = $id == 0;
        
        $this->add_inner(new CHtmlHidden('id', $id));
        $this->add_inner(new CHtmlHidden('id_owner', $id_owner));
        
        $table = new CHtmlTable(array('cellpadding'=>'10', 'class'=>'form', 'style'=>'background-color:white;'));
        $this->add_inner($table);
        
        $label = Operator::label();
        $style = 'color:white;background-color:grey;width:20px;height:20px;float:right;cursor:pointer;';
        $title = $is_new ? 'Add' : 'Edit';
        $title = "<b>{$title} {$label} Group</b><div style='{$style}'>x</div>";
        $attrs = array('colspan'=>'100%', 'align'=>'center');
        $this->hotspot = $table->add_row($title, array_merge($attrs, array('style'=>'border-bottom:2px solid #CDA000;')));
        
        $td = $table->make_row_td(0, 'Name', array('align'=>'right', 'style'=>'padding-right:0px;'));
        $table->add_row(array($td, new CHtmlText('name', $name, array('style'=>'width:85%;'))));
        
        $ctrl = new AssignOperatorsCtrl($args);
        $table->add_row($ctrl, $attrs);
        
        $ctrl = new AssignZonesCtrl($args);
        $table->add_row($ctrl, $attrs);
        
        $ctrl = new CHtmlSubmit('Submit', array('style'=>'width:120px;'));
        $ctrl->disable_on_click();
        $table->add_row($ctrl, $attrs);
    }
}
?>