<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Not Authorized View */
class NotAuthorizedView extends CView
{
    /** Initialize view contents */
    protected function init_contents(array $args)
    {
        $str = 'You are not authorized to view this page. Please login.';
        $el = new CHtmlElement('div', array('class'=>'error'), $str);
        $this->add_inner($el);
    }
}
?>