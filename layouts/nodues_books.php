<?php
  require_once("../classes/Query.php");
  require_once("../classes/DmQuery.php");
  require_once("../functions/errorFuncs.php");
  require_once("../classes/BiblioCopy.php");
  require_once('../classes/MemberQuery.php');
  require_once('../classes/BiblioCopyQuery.php');

class Layout_duedate_email {
  function render($rpt) {
    list($rpt, $errs) = $rpt->variant_el(array('order_by'=>'member'));
    if (!empty($errs)) {
      Fatal::internalError('Unexpected report error');
    }
    
    $mbrQ = new MemberQuery;
    $copyQ = new BiblioCopyQuery;
    $mbr = NULL;
    $oldmbrid = NULL;
    $oldmbr = NULL;
    $msgBodym = NULL;
    while ($row = $rpt->each()) {
      $copy = $copyQ->queryByBarcode($row['barcode_nmbr']);
//      if(($row['days_late'] == '-2' || 
//	      $row['days_late'] == '-1'|| 
//	      ($row['days_late'] == '0' && 
//	      date('H') < 20)) &&
//	      $copy->getLastMailDt() != date('Y-m-d')) {
        if(0) {
        if ($row['mbrid'] != $oldmbrid) {
          if ($oldmbrid !== NULL) {
            $to=$oldmbr->getFirstName().' '.$oldmbr->getLastName()." <".$oldmbr->getEmail().">";
            $subject = "[Book Club] Registration Desk";
            $msgBodys="Dear ".$oldmbr->getFirstName().' '.$oldmbr->getLastName().",\n";
      $msgBodys.="\nThe book/s, as detailed below, are issued on your name. We have to close the Book club from 14/07/2012 to 23/07/2012 because of absence of Coordinator/Secretaries during this period. Since book club will be closed from 15 to registration time no fine will be taken for this period. We will set up a desk at registration time to collect the books and fines. Book club will also open on 24/07/2012 so that you can come take your No-Dues which may be more confortable for some of you. Without No-Dues from book club you will not be able to register for the new semester.\n\n";
            $msgBodys.="You have a fine of Rs. ".$oldrow['balance']." till now. You will have to pay Rs. ".$oldrow['total']." if you return the book/s on your registration date(including fine for currently issued overdue books)\n\n";
            $msgBodys.="You have following book/s issued on your account.\n";
            $body = $msgBodys.$msgBodym.$msgBodye;
            require_once("../shared/send_mail.php");
            $msgBodym=NULL;
          }
          $mbr = $mbrQ->get($row['mbrid']);
          $oldrow = $row;
          $oldmbr = $mbr;
          $oldmbrid = $row['mbrid'];
        }
        $copy = $copyQ->queryByBarcode($row['barcode_nmbr']);
        $copy->setLastMailDt(date("Y-m-d"));
        $copyQ->update($copy);
        $msgBodym.="\"".$row['title']."\" by ";
        $msgBodym.="\"".$row['author']."\" with due date ";
        $msgBodym.=date('d/m/Y', strtotime($row['due_back_dt'])).".\n";
      }
//      }
    }
    if ($oldmbrid !== NULL) {
      $to=$oldmbr->getFirstName().' '.$oldmbr->getLastName()." <".$oldmbr->getEmail().">";
      $subject = "[Book Club] Registration Desk";
      $msgBodys="Dear ".$oldmbr->getFirstName().' '.$oldmbr->getLastName().",\n";
      $msgBodys.="\nThe book/s, as detailed below, are issued on your name. We have to close the Book club from 14/07/2012 to 23/07/2012 because of absence of Coordinator/Secretaries during this period. Since book club will be closed from 15 to registration time no fine will be taken for this period. We will set up a desk at registration time to collect the books and fines. Book club will also open on 24/07/2012 so that you can come take your No-Dues which may be more confortable for some of you. Without No-Dues from book club you will not be able to register for the new semester.\n\n";
      $msgBodys.="You have a fine of Rs. ".$oldrow['balance']." till now. You will have to pay Rs. ".$oldrow['total']." if you return the book/s on your registration date(including fine for currently issued overdue books)\n\n";
      $msgBodys.="You have following book/s issued on your account.\n";
      $body = $msgBodys.$msgBodym.$msgBodye;
      require_once("../shared/send_mail.php");
      $msgBodym=NULL;
    }
  }
}
?>
