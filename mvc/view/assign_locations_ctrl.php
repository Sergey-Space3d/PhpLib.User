<?php
// Copyright 2005-2021 Sergey Shustov.
// License: GPL v2.  See License.txt.

class AssignLocationsCtrl extends CHtmlElement
{
    /** The constructor */
    public function __construct(array $args, array $attrs = null, $inner = null)
    {
        parent::__construct('span', $attrs, $inner);
        
        @extract($args);
        
        $table = new CHtmlTable(array('cellpadding'=>'3'));
        $this->add_inner($table);

        $table->add_row(array('Available locations', '&nbsp;', 'Assigned locations'),
            array('style'=>'padding-bottom:0px;', 'align'=>'center'));
        
        // This element holds ids of assigned locations
        $assigned_ids = array_keys($assigned_locations);
        $el = new CHtmlHidden('assigned_location_ids', implode(',', $assigned_ids));
        $elid_hidden = $el->get_id(true);
        $this->add_inner($el);
        
        $attrs = array('size'=>'5', 'style'=>'width:170px;height:7em;overflow-x:auto;');
        $ctrl_available = new CHtmlCbox('ctrl1', 0, $attrs);
        $ctrl_available->add_items($available_locations);
        $elid_available = $ctrl_available->get_id(true);
        
        $ctrl_assigned = new CHtmlCbox('ctrl2', 0, $attrs);
        $ctrl_assigned->add_items($assigned_locations);
        $elid_assigned = $ctrl_assigned->get_id(true);
        
        $div = new CHtmlElement('div');
        $ctrl_in = new CHtmlButton('>>');
        $ctrl_in->set_attr('onclick', "assign_locations(\"{$elid_hidden}\", \"{$elid_available}\", \"{$elid_assigned}\", true)");
        $div->add_inner($ctrl_in);
        
        $ctrl_out = new CHtmlButton('<<', array('style'=>'display:block;margin-top:5px;'));
        $ctrl_out->set_attr('onclick', "assign_locations(\"{$elid_hidden}\", \"{$elid_available}\", \"{$elid_assigned}\", false)");
        $div->add_inner($ctrl_out);
        
        $table->add_row(array($ctrl_available, $div, $ctrl_assigned));
        
        // Create JS to manage locations ------------------------------------
        
        if (!CHtmlJavaScript::has_function('assign_locations'))
        {
            $lines = array(
                "var el_hidden = document.getElementById(elid_hidden);",
                "var el_available = document.getElementById(elid_available);",
                "var el_assigned = document.getElementById(elid_assigned);",
                "var el_from = assign ? el_available : el_assigned;",
                "var el_to = assign ? el_assigned : el_available;",
                "var sel = el_from.value;",
                "if (!sel) alert('Select location from the list');",
                "else",
                "{",
                "var el = el_from.options[el_from.selectedIndex];",
                "el_to.add(el);",
                "el_from.remove(el_from.selectedIndex);",
                "var arr = [];",
                "for (var i = 0; i < el_assigned.options.length; i++)",
                "{",
                "arr.push(el_assigned.options[i].value);",
                "}",
                "el_hidden.value = arr.join(',');",
                "}",
            );
            
            CHtmlPage::get_js()->add_function('assign_locations', $lines, array('elid_hidden', 'elid_available', 'elid_assigned', 'assign'));
        }
    }
}
?>