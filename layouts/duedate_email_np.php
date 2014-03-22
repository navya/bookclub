<?php
  require_once("../classes/Query.php");
  require_once("../classes/DmQuery.php");
  require_once("../functions/errorFuncs.php");
  require_once("../classes/BiblioCopy.php");
  require_once('../classes/MemberQuery.php');
  require_once('../classes/BiblioCopyQuery.php');
  require_once "Mail.php";
  require_once("../shared/mail_vars.php");

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
      if(($row['days_late'] == '-2' || 
	      $row['days_late'] == '-1'|| 
	      ($row['days_late'] == '0' && 
	      date('H') < 20)) && 
	      $copy->getLastMailDt() != date('Y-m-d')) {
        if ($row['mbrid'] != $oldmbrid) {
          if ($oldmbrid !== NULL) {
            $to=$oldmbr->getFirstName().' '.$oldmbr->getLastName()." <".$oldmbr->getEmail().">";
            $subject = "[Book Club] Due Date Alert";
            $msgBodys="Dear ".$oldmbr->getFirstName().' '.$oldmbr->getLastName().",\n";
            $msgBodys.="\nThe book/s, as detailed below, issued on your name will be due for return shortly. You are advised to return it to the book club on or before the due date.\n\n";
	    require_once("../shared/send_mail.php");
            $msgBodym=NULL;
          }
          $mbr = $mbrQ->get($row['mbrid']);
          $oldmbr=$mbr;
          $oldmbrid = $row['mbrid'];
        }
        $copy = $copyQ->queryByBarcode($row['barcode_nmbr']);
        $copy->setLastMailDt(date("Y-m-d"));
        $copyQ->update($copy);
        $msgBodym.="\"".$row['title']."\" by ";
        $msgBodym.="\"".$row['author']."\" due date ";
        $msgBodym.=date('d/m/Y', strtotime($row['due_back_dt'])).".\n";
      }
    }
    if ($oldmbrid !== NULL) {
      $to=$oldmbr->getFirstName().' '.$oldmbr->getLastName()." <".$oldmbr->getEmail().">";
      $subject = "[Book Club] Due Date Alert";
      $msgBodys="Dear ".$oldmbr->getFirstName().' '.$oldmbr->getLastName().",\n";
      $msgBodys.="\nThe book/s, as detailed below, issued on your name will be due for return shortly. You are advised to return it to the book club on or before the due date.\n\n";
      require_once("../shared/send_mail.php");
      
      $msgBodym=NULL;
    }
  }
}
?>
