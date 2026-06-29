<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Delete Operator Controller */
class DeleteOperatorController extends CController
{
    /** Process the form */
    protected function process()
    {
        $id_operator = (int)$this->get_value('id');
        $operator = new Operator($id_operator);
        
        $name = "{$operator->get_name()} {$operator->get_last_name()}";
        $label = Operator::label();
        
        $success = ServerAdmin::Instance()->delete_operator($id_operator);

        if ($success)
        {
            CHtmlPage::set_last_info("{$label} {$name} was deleted");
            return true;
        }
        
        CHtmlPage::set_last_error("Failed to delete {$label} {$name}");
        return false;
    }
}
?>