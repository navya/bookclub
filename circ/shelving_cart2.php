<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
require_once("../shared/common.php");
$tab = "circulation";
$nav = "checkin";
$restrictInDemo = true;
require_once("../shared/logincheck.php");
require_once("../classes/BiblioQuery.php");
require_once('../classes/MemberQuery.php');
require_once('../classes/BiblioCopyQuery.php');
require_once('../classes/BiblioHoldQuery.php');
require_once('../classes/BiblioHold.php');

require_once("../functions/errorFuncs.php");
require_once("../classes/CircQuery.php");
require_once("../functions/formatFuncs.php");
require_once("../classes/Localize.php");
$loc = new Localize(OBIB_LOCALE,$tab);

if (count($_POST) == 0) {
  header("Location: ../circ/checkin_form.php?reset=Y");
  exit();
}
$barcode = trim($_POST["barcodeNmbr"]);
$circQ = new CircQuery();
$copyQ = new BiblioCopyQuery();
$bibQ = new BiblioQuery();
$copyQ->connect();
if(!$copy = $copyQ->queryByBarcode($barcode)) {
  $copyQ->close();
  header("Location: ../circ/checkin_form.php");
}
else if(!is_a($copy, 'BiblioCopy')){
  $mbrid = 0;
}
else {
  $mbrid = $copy->getMbrid();
}
	  
  


$circQ = new CircQuery();
list($info, $err) = $circQ->shelving_cart_e($barcode);
if ($err) $err = $err->toStr();
  
if ($err) {
  $postVars["barcodeNmbr"] = $barcode;
  $pageErrors["barcodeNmbr"] = $err;
  $_SESSION["postVars"] = $postVars;
  $_SESSION["pageErrors"] = $pageErrors;
  header("Location: ../circ/checkin_form.php");
  exit();
}
else {
  if($mbrid!=NULL && $mbrid!=0) {
    $mbrQ = new MemberQuery();
    $mbr = $mbrQ->get($mbrid);
    $to=$mbr->getFirstName().' '.$mbr->getLastName()." <".$mbr->getEmail().">";
    $biblio = $bibQ->doQuery($copy->getBibid());
    $biblioFlds = $biblio->getBiblioFields();
    $subject = "[Book Club] Book Returned";
    $msgBodys="Dear ".$mbr->getFirstName().' '.$mbr->getLastName().",\n";
    $msgBodym="\nThe book \"".$biblioFlds["245a"]->getFieldData()."\" by \"".$biblioFlds["100a"]->getFieldData()."\" has been just returned to the book club today at ".date("g:i A").".\n";
    $body = $msgBodys.$msgBodym.$msgBodye;
    require_once("../shared/send_mail.php");
  }
  $hold=$info['hold'];
  if($hold) {
    $holdQ = new BiblioHoldQuery();
    $holdQ->connect();
    if ($holdQ->errorOccurred()) {
      $holdQ->close();
      displayErrorPage($holdQ);
    }
    if (!$holdQ->queryByBibid($bibid)) {
      $holdQ->close();
      displayErrorPage($holdQ);
    }
    if ($holdQ->getRowCount() != 0) {
      $to="rajivk@iitk.ac.in, ";
      /* while ($hold = $holdQ->fetchRow()) {		 */
      /* 	$to.=$hold->getEmail.", "; */
      /* } */
      $biblio = $bibQ->doQuery($copy->getBibid());
      $biblioFlds = $biblio->getBiblioFields();
      $subject = "[Book Club] Book Available";
      $msgBodys="Dear Member,";
      $msgBodym="\nThe book \"".$biblioFlds["245a"]->getFieldData()."\" by \"".$biblioFlds["100a"]->getFieldData()."\" is now available in the book club. This book is reserved for all the members who have put hold on this book. You can come and issue this book within two days after checking its status on site.\n";
      $body = $msgBodys.$msgBodym.$msgBodye;
      require_once("../shared/send_mail.php");
    }    
  }
}

unset($_SESSION["postVars"]);
unset($_SESSION["pageErrors"]);

$params = "?barcode=".U($barcode);
if ($info['mbrid']) {
  $params .= "&mbrid=".U($info['mbrid']);
}
if ($info['late']) {
  $params .= "&late=".U($info['late']);
}
if ($info['hold']) {
  header("Location: ../circ/hold_message.php".$params);
} else {
  header("Location: ../circ/checkin_form.php".$params);
}
