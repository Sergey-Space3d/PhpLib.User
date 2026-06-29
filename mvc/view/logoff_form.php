<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Logoff Form */
class LogoffForm extends CForm
{
	/** Initialize form contents */
	protected function init_contents(array $args)
	{
	    @extract($args);
	    
	    $table = new CHtmlTable(array('cellpadding'=>'0', 'cellspacing'=>'0', 'border'=>'0'));
	    $this->add_inner($table);
	    
	    $tr = new CHtmlElement('tr');
	    $table->add_inner($tr);
	    
	    $el = "{$name}&nbsp;&nbsp;&nbsp;&nbsp;";
	    $tr->add_inner(new CHtmlElement('td', array('style'=>'white-space:nowrap;color:white;'), $el));
	    
	    $el = new CHtmlSubmit('Logoff');
	    $el->disable_on_click();
	    $tr->add_inner(new CHtmlElement('td', null, $el));
	}
}
?>