<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Delete Operator Group Controller */
class DeleteOperatorGroupController extends CController
{
    /** Process the form */
    protected function process()
    {
        $id = (int)$this->get_value('id');
        $group = OperatorGroupMan::Instance()->get_ledger($id);
        $label = Operator::label();
        
        if (!$group)
        {
            CHtmlPage::set_last_error("Failed to delete {$label->lower()} group");
            return false;
        }

        if (ServerAdmin::Instance()->delete_operator_group($id))
        {
            CHtmlPage::set_last_info("{$label} group {$group->get_name()} was deleted");
            return true;
        }
        
        CHtmlPage::set_last_error("Failed to delete {$label->lower()} group {$group->get_name()}");
        return false;
    }
}
?>