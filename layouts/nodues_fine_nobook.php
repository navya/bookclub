<?php
  require_once("../classes/Query.php");
  require_once("../classes/DmQuery.php");
  require_once("../functions/errorFuncs.php");
  require_once "Mail.php";
  require_once("../shared/mail_vars.php");
class Layout_nodues_fine_nobook {
  function render($rpt) {
    list($rpt, $errs) = $rpt->variant_el(array('order_by'=>'member'));
    if (!empty($errs)) {
      Fatal::internalError('Unexpected report error');
    }
//        if(0) {
    while ($row = $rpt->each()) {
      $to=$row['name']." <".$row['email'].">";
      $subject = "[Book Club] Registration Desk Information";
      $msgBodys="Dear ".$row['name'].",\n";
      $msgBodys.="\nWe have to close the Book club from 14/07/2012 to 23/07/2012 because of absence of Coordinator/Secretaries during this period. We will set up a desk at registration time to collect the books and fines. Book club will also open on 24/07/2012 so that you can come take your No-Dues which may be more confortable for some of you. Without No-Dues from book club you will not be able to register for the new semester.\n\n";
      $msgBodys.="You currently have a fine of Rs. ".$row['balance']."\n";
      require_once("../shared/send_mail.php");
//      }
    }
  }
}
?>
