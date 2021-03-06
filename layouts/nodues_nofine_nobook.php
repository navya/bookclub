<?php
  require_once("../classes/Query.php");
  require_once("../classes/DmQuery.php");
  require_once("../functions/errorFuncs.php");

class Layout_nodues_nofine_nobook {
  function render($rpt) {
    list($rpt, $errs) = $rpt->variant_el(array('order_by'=>'member'));
    if (!empty($errs)) {
      Fatal::internalError('Unexpected report error');
    }
        if(0) {
    while ($row = $rpt->each()) {
      $to=$row['name']." <".$row['email'].">";
      $subject = "Book Club Closed";
      $msgBodys="Dear ".$row['name'].",\n";
      $msgBodys.="\nWe have to close the Book club from 14/07/2012 to 23/07/2012 because of absence of Coordinator/Secretaries during this period.\n";
      $msgBodys.="You currently have no books or fine in your account.\n";
      $body = $msgBodys.$msgBodye;
      require_once("../shared/send_mail.php");
      }
    }
  }
}
?>
