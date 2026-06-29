<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Login Form */
class LoginForm extends CForm
{
	/** Initialize form contents */
	protected function init_contents(array $args)
	{
		@extract($args);
		
		if ($slim)
		{
    		$table = new CHtmlTable(array('cellpadding'=>'3', 'cellspacing'=>'0', 'border'=>'0', 
    		    'class'=>'form', 'style'=>'border-radius:10px;'));	
    		$this->add_inner($table);
    		
    		$tr = new CHtmlElement('tr');
    		$table->add_inner($tr);
    		
    		$input_attrs = array('style'=>'width:120px;');
    		$ctrl = new CHtmlText('login', $login, $input_attrs);
    		$this->setup_hint($ctrl, 'User Name');
    		$tr->add_inner(new CHtmlElement('td', array('align'=>'left'), $ctrl));
    		
    		$ctrl = new CHtmlPassword('password', $password, $input_attrs);
    		$this->setup_hint($ctrl, 'Password', true);
    		$tr->add_inner(new CHtmlElement('td', array('align'=>'left'), $ctrl));
    		
    		$ctrl = new CHtmlSubmit('Login', array('style'=>'width:80px;'));
    		$ctrl->disable_on_click();
    		$tr->add_inner(new CHtmlElement('td', array('align'=>'center'), $ctrl));
		}
		else 
		{
		    $table = new CHtmlTable(array('cellpadding'=>'20', 'cellspacing'=>'0', 'border'=>'0',
		        'class'=>'form', 'style'=>'margin-top:60px;'));
		    $this->add_inner(CHtmlElement::wrap($table, 'center'));
		    
		    $tr = new CHtmlElement('tr');
		    $table->add_inner($tr);
		    $td = new CHtmlElement('td');
		    $tr->add_inner($td);
		    
		    $input_attrs = array('style'=>'width:280px;');
		    $td->add_inner(new CHtmlElement('span', array('class'=>'form_label'), 'Username'));
		    $td->add_inner('<br/>');
		    $ctrl = new CHtmlText('login', $login, $input_attrs);
		    //$this->setup_hint($ctrl, 'User Name');
		    $td->add_inner($ctrl);
		    
		    $tr = new CHtmlElement('tr');
		    $table->add_inner($tr);
		    $td = new CHtmlElement('td');
		    $tr->add_inner($td);
		    
		    $td->add_inner(new CHtmlElement('span', array('class'=>'form_label'), 'Password'));
		    $td->add_inner('<br/>');
		    $ctrl = new CHtmlPassword('password', $password, $input_attrs);
		    //$this->setup_hint($ctrl, 'Password', true);
		    $td->add_inner($ctrl);
		    
		    $tr = new CHtmlElement('tr');
		    $table->add_inner($tr);
		    $ctrl = new CHtmlSubmit('Login', array('style'=>'width:80px;'));
		    $ctrl->disable_on_click();
		    $tr->add_inner(new CHtmlElement('td', array('align'=>'right', 'style'=>'padding-top:0px;'), $ctrl));
		}
	}
}
?>