<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
require_once("../shared/common.php");
require_once("../classes/BiblioCopyQuery.php");
require_once("../classes/BiblioQuery.php");
require_once("../classes/MemberQuery.php");
require_once "Mail.php";
require_once("../shared/mail_vars.php");

#****************************************************************************
#*  Checking for get vars.
#****************************************************************************
$bibid = $_GET["bibid"];
$copyid = $_GET["copyid"];
$holdid = $_GET["holdid"];
$mbrid = $_GET["mbrid"];
if ($mbrid == "") {
$tab = "cataloging";
$nav = "holds";
$returnNav = "../catalog/biblio_hold_list.php?bibid=".U($bibid);
} else {
$tab = "circulation";
$nav = "view";
$returnNav = "../circ/mbr_view.php?mbrid=".U($mbrid);
}
$restrictInDemo = TRUE;
require_once("../shared/logincheck.php");
require_once("../classes/BiblioHoldQuery.php");
require_once("../functions/errorFuncs.php");
require_once("../classes/Localize.php");
$loc = new Localize(OBIB_LOCALE,"shared");

#**************************************************************************
#*  Delete hold
#**************************************************************************
// we need to also insert into status history table
$holdQ = new BiblioHoldQuery();
$holdQ->connect();
if ($holdQ->errorOccurred()) {
  $holdQ->close();
  displayErrorPage($holdQ);
}
$rc = $holdQ->delete($bibid,$copyid,$holdid);
if (!$rc) {
  $holdQ->close();
  displayErrorPage($copyQ);
}

$copyQ = new BiblioCopyQuery();
$copyQ->connect();
$copy = $copyQ->doQuery($bibid,$copyid);
if(!$holdQ->maybeGetFirstHold($bibid,$copyid)) {
  if ($copy->getStatusCd() == OBIB_STATUS_ON_HOLD) {
    $copy->setStatusCd(OBIB_STATUS_SHELVING_CART);
    $copyQ->updateStatus($copy);
  }
  $copyQ->close();
}
else if ($copy->getStatusCd() == OBIB_STATUS_ON_HOLD) {
  $hold=$holdQ->maybeGetFirstHold($bibid,$copyid);
  if($hold) {
    $mbrid=$hold->getMbrid();
    $bibQ = new BiblioQuery();
    if($mbrid!=NULL && $mbrid!=0) {
      $mbrQ = new MemberQuery();
      $mbr = $mbrQ->get($mbrid);
      $to=$mbr->getFirstName().' '.$mbr->getLastName()." <".$mbr->getEmail().">";
      $biblio = $bibQ->doQuery($copy->getBibid());
      $biblioFlds = $biblio->getBiblioFields();
      $subject = "[Book Club] Book Available";
      $msgBodys="Dear ".$mbr->getFirstName().' '.$mbr->getLastName().",\n";
      $msgBodym="\nThe book \"".$biblioFlds["245a"]->getFieldData()."\" by \"".$biblioFlds["100a"]->getFieldData()."\" is now available in the book club. You can come and issue this book within two days.\n";
      $body = $msgBodys.$msgBodym.$msgBodye;
      require_once("../shared/send_mail.php");
    }    
  }
  $holdQ->close();
}
#**************************************************************************
#*  Go back to member view
#**************************************************************************
$msg = $loc->getText("holdDelSuccess");
header("Location: ".$returnNav."&msg=".U($msg));
?>
