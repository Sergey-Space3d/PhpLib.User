<?php
// Copyright 2005-2021 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Edit Operator Access Form */
class EditOperatorAccessForm extends FormLayout
{
    /** Initialize form contents */
    protected function init_contents(array $args)
    {
        @extract($args);
        $is_new = $id == 0;
        
        $this->add_inner(new CHtmlHidden('id', $id));

        $title = $is_new ? 'Add Operator Access' : 'Edit Operator Access';
        $this->set_title('<b>'.$title.'</b>');
        if ($name) $this->add_row($name, array('style'=>'text-align:center;background-color:#555555;color:white;'));
        
        if ($access_links)
        {
            $this->add_access_ctrls($access_links, $operator_access);
            if ($access_nolinks) $this->add_hr();
        }
        
        if ($access_nolinks)
        {
            $this->add_access_ctrls($access_nolinks, $operator_access);
        }
        
        $ctrl = new CHtmlSubmit('Submit');
        $ctrl->disable_on_click();
        $this->add_control_deck($ctrl);
    }
    
    /** Add access checkboxes */
    protected function add_access_ctrls(array $access, array $operator_access)
    {
        $this->align_label = 'left';
        $access_ctrls = array();
        
        foreach ($access as $key=>$val)
        {
            $checked = in_array($key, $operator_access);
            $ctrl = new CHtmlCheckbox('operator_access', $key, $checked);
            $ctrl->add_inner($val);
            
            $access_ctrls[] = $ctrl;
            
        }
        
        for ($i = 0; $i < count($access_ctrls); $i += 2)
        {
            $ctrl1 = $access_ctrls[$i];
            $ctrl2 = $access_ctrls[$i + 1];
            $this->add_control($ctrl1, $ctrl2);
        }
    }
}
?>