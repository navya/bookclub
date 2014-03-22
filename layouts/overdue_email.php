<?php
require_once('../classes/MemberQuery.php');
require_once "Mail.php";
require_once("../shared/mail_vars.php");

class Layout_overdue_email {
  function render($rpt) {
    list($rpt, $errs) = $rpt->variant_el(array('order_by'=>'member'));
    if (!empty($errs)) {
      Fatal::internalError('Unexpected report error');
    }
    
    $mbrQ = new MemberQuery;
    $mbr = NULL;
    $oldmbrid = NULL;
    $oldmbr = NULL;
    $msgBodym = NULL;
    while ($row = $rpt->each()) {
    $mbr = $mbrQ->get($row['mbrid']);
    if(strtotime($mbr->getLastEmailDt()) <= mktime(0, 0, 0, date("m"), date("d")-2, date("Y"))) {
      if ($row['mbrid'] != $oldmbrid) {
        if ($oldmbrid !== NULL) {
          $to=$oldmbr->getFirstName().' '.$oldmbr->getLastName()." <".$oldmbr->getEmail().">";
          $subject = "[Book Club] Overdue Book Alert";
          $msgBodys="Dear ".$oldmbr->getFirstName().' '.$oldmbr->getLastName().",\n";
          $msgBodys=$msgBodys."\nOur records show that the following library items are checked out under your name and are past due. Please return them as soon as possible.\n\n";
	  require_once("../shared/send_mail.php");
          $oldmbr->setLastEmailDt(date("Y-m-d"));
          $mbrQ->update($oldmbr);
          $msgBodym=NULL;
        }
        $mbr = $mbrQ->get($row['mbrid']);
        $oldmbr=$mbr;
        $oldmbrid = $row['mbrid'];
      }
      $msgBodym=$msgBodym."\"".$row['title']."\" by ";
      $msgBodym=$msgBodym."\"".$row['author']."\" due date ";
      $msgBodym=$msgBodym."\"".date('m/d/y', strtotime($row['due_back_dt']))."\" days late ";
      $msgBodym=$msgBodym.$row['days_late']."\n";
    }
    }
        if ($oldmbrid !== NULL) {
          $to=$oldmbr->getFirstName().' '.$oldmbr->getLastName()." <".$oldmbr->getEmail().">";
          $subject = "[Book Club] Overdue Book Alert";
          $msgBodys="Dear ".$oldmbr->getFirstName().' '.$oldmbr->getLastName().",\n";
          $msgBodys=$msgBodys."\nOur records show that the following library items are checked out under your name and are past due. Please return them as soon as possible.\n\n";
	  require_once("../shared/send_mail.php");

          $oldmbr->setLastEmailDt(date("Y-m-d"));
          $mbrQ->update($oldmbr);
          $msgBodym=NULL;
        }
  }
}
?>
