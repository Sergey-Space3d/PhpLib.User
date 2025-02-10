<?php
// Copyright 2005-2021 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Edit Location Controller */
class EditLocationController extends CController
{
    /** Initialize the form */
    protected function initialize()
    {
        $id = $this->get_arg('id');
        $location = new Location($id);
        
        $this->set_arg('name', $this->choose_value($location->get_name(), 'name'));
        $this->set_arg('external_id', $this->choose_value($location->get_external_id(), 'external_id'));
        $this->set_arg('street_number', $this->choose_value($location->get_street_number(), 'street_number'));
        $this->set_arg('street_name', $this->choose_value($location->get_street_name(), 'street_name'));
        $this->set_arg('unit_number', $this->choose_value($location->get_unit_number(), 'unit_number'));
        $this->set_arg('city', $this->choose_value($location->get_city(), 'city'));
        $this->set_arg('state', $this->choose_value($location->get_state(), 'state'));
        $this->set_arg('zip', $this->choose_value($location->get_zip(), 'zip'));
        if (!$location->get_country()) $location->set_country('US'); // US by default
        $this->set_arg('country', $this->choose_value($location->get_country(), 'country'));
        $this->set_arg('latitude', $this->choose_value($location->get_latitude(), 'latitude'));
        $this->set_arg('longitude', $this->choose_value($location->get_longitude(), 'longitude'));
        
        // TODO: for now, the only valid country is US
        $this->set_arg('states', Geo::get_states('US'));
        $this->set_arg('countries', array('US'=>'United States'));
        
        // Set owner fields
        $id_owner = $this->get_arg('id_owner');
        
        if ($id_owner)
        {
            $owner = new Owner($id_owner);
            $this->set_arg('owner_name', $owner->get_name()); // read-only
        }
        
        $this->enable_post(true);
    }
    
    /** Process the form */
    protected function process()
    {
        $id = (int)$this->get_value('id');
        $id_owner = (int)$this->get_value('id_owner');
        $id_zone = $this->get_value('id_zone');
        
        $label = Location::label();
        $owner_label = Owner::label();
        
        if ($id_owner == 0)
        {
            CHtmlPage::set_last_error("No {$owner_label} is specified for the {$label}");
            return false;
        }
        
        $name = $this->get_value('name');
        $external_id = $this->get_value('external_id');
        $street_number = $this->get_value('street_number');
        $street_name = $this->get_value('street_name');
        $unit_number = $this->get_value('unit_number');
        $city = $this->get_value('city');
        $state = $this->get_value('state');
        $zip = $this->get_value('zip');
        $country = $this->get_value('country');
        $latitude = $this->get_value('latitude');
        $longitude = $this->get_value('longitude');
        
        if (strlen($name) < 2 || strlen($name) > 32)
        {
            CHtmlForm::set_error('Name should be between 2 and 32 characters', 'name');
        }
        else if (LocationMan::Instance()->is_name_reserved($name, $id, $id_owner))
        {
            CHtmlForm::set_error("Name '{$name}' is already used", 'name');
        }
        
        if ($this->get_value('use_external_id'))
        {
            if (!$external_id)
            {
                CHtmlForm::set_error('Id is invalid', 'external_id');
            }
            else if (LocationMan::Instance()->is_external_id_reserved($external_id, $id, $id_owner))
            {
                CHtmlForm::set_error("Id '{$external_id}' is already used", 'external_id');
            }
        }
        
        if (strlen($street_number) < 1) CHtmlForm::set_error('Street number is invalid', 'street_number');
        if (strlen($street_name) < 1) CHtmlForm::set_error('Street name is invalid', 'street_name');
        if (strlen($city) < 2) CHtmlForm::set_error('City name is invalid', 'city');
        if (strlen($state) != 2) CHtmlForm::set_error('State code is invalid', 'state');
        if (strlen($country) != 2) CHtmlForm::set_error('Country code is invalid', 'country');
        
        $errors = CHtmlForm::get_errors();
        
        if ($errors)
        {
            CHtmlPage::set_last_error(implode('<br/>', $errors));
            return false;
        }
        
        $location = new Location($id);
        $location->set_owner_id($id_owner);
        $location->set_name($name);
        $location->set_external_id($external_id);
        $location->set_street_number($street_number);
        $location->set_street_name($street_name);
        $location->set_unit_number($unit_number);
        $location->set_city($city);
        $location->set_state($state);
        $location->set_zip($zip);
        $location->set_country($country);
        $location->set_latitude($latitude);
        $location->set_longitude($longitude);
        
        if (!ServerAdmin::Instance()->edit_location($location, $id_zone))
        {
            $action = $id ? 'update' : 'add';
            CHtmlPage::set_last_error("Failed to {$action} {$label} {$name}");
            return false;
        }
        
        if ($id == 0)
        {
            // Reset the selector
            CHtmlForm::set_value(SELECTOR_LOCATION_ID, $location->get_id(), true);
        }
        
        $action = $id ? 'updated' : 'added';
        CHtmlPage::set_last_info("{$label} {$name} was {$action}");
        
        return true;
    }
}
?>