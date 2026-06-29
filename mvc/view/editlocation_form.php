<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Edit Location Form */
class EditLocationForm extends FormLayout
{
    /** Initialize form contents */
    protected function init_contents(array $args)
    {
        @extract($args);
        
        $is_new = $id == 0;
        $this->add_inner(new CHtmlHidden('id', $id));
        $this->add_inner(new CHtmlHidden('id_owner', $id_owner));
        $this->add_inner(new CHtmlHidden('id_zone', $id_zone));
        
        $label = Location::label();
        $title = $is_new ? "Add {$label}" : "Edit {$label}";
        $this->set_title('<b>'.$title.'</b>');
        
        if ($owner_name)
        {
            $this->add_control(Owner::label(), $owner_name);
            $this->add_hr();
        }
        
        $state_cbox = new CHtmlCbox('state', $state);
        foreach ($states as $key=>$val)
        {
            $state_cbox->add_item($val, $key);
        }

        $country_cbox = new CHtmlCbox('country', $country);
        foreach ($countries as $key=>$val)
        {
            $country_cbox->add_item($val, $key);
        }
        
        $this->add_control(Owner::label(), $owner_name);
        $this->add_control("{$label} Name", new CHtmlText('name', $name), true, true);
        
        if ($use_external_id)
        {
            $this->add_control("{$label} Id", new CHtmlText('external_id', $external_id), true, true);
            $this->add_inner(new CHtmlHidden('use_external_id', true));
        }
        else
        {
            $this->add_inner(new CHtmlHidden('external_id', $external_id));
        }
        
        $this->add_control('Street Number', new CHtmlText('street_number', $street_number), true, true);
        $this->add_control('Street Name', new CHtmlText('street_name', $street_name), true, true);
        $this->add_control('Unit Number', new CHtmlText('unit_number', $unit_number));
        $this->add_control('City', new CHtmlText('city', $city), true, true);
        $this->add_control('State', $state_cbox, true, true);
        $this->add_control('Zip', new CHtmlText('zip', $zip));
        $this->add_control('Country', $country_cbox, true, true);
        
        if ($use_latitude)
        {
            $this->add_hr();
            
            $this->add_control('Latitude', new CHtmlText('latitude', $latitude));
            $this->add_control('Longitude', new CHtmlText('longitude', $longitude));
        }
        else
        {
            $this->add_inner(new CHtmlHidden('latitude', $latitude));
            $this->add_inner(new CHtmlHidden('longitude', $longitude));
        }
        
        $ctrl = new CHtmlSubmit('Submit');
        $ctrl->disable_on_click();
        $this->add_control_deck($ctrl);
    }
}
?>