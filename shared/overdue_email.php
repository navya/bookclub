<?php
require_once('../classes/Lay.php');
require_once('../classes/MemberQuery.php');


class Layout_overdue {
  function render($rpt) {
    list($rpt, $errs) = $rpt->variant_el(array('order_by'=>'member'));
    if (!empty($errs)) {
      Fatal::internalError('Unexpected report error');
    }








?>
