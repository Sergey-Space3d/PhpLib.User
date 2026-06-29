<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Login As Form */
class LoginAsForm extends CForm
{
    /** Initialize form contents */
    protected function init_contents(array $args)
    {
        @extract($args);
        
        if ($action) $this->set_attr('action', $action);
        
        $this->add_inner(new CHtmlHidden('login', $login));
        $this->add_inner(new CHtmlHidden('password', $password));
        $this->add_inner(new CHtmlHidden('access', $access));
        
        // This value will be passed to the constructor of LoggableAccess
        $this->add_inner(new CHtmlHidden('__logas__', true));
        
        if (!$title) $title = 'Login As';
        $ctrl = new CHtmlSubmit($title, array('class'=>($class ? $class : 'deck')));
        $ctrl->disable_on_click();
        $this->add_inner($ctrl);
    }
}
?>