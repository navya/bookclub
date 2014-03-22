<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
require_once("../shared/common.php");
  
#****************************************************************************
#*  Checking for get vars.  Go back to form if none found.
#****************************************************************************
if (count($_GET) == 0) {
  header("Location: ../catalog/index.php");
  exit();
}

#****************************************************************************
#*  Checking for tab name to show OPAC look and feel if searching from OPAC
#****************************************************************************
if (isset($_GET["tab"])) {
  $tab = $_GET["tab"];
} else {
  $tab = "cataloging";
}

$nav = "view";
require_once("../classes/BiblioQuery.php");
require_once("../classes/BiblioCopyQuery.php");
require_once("../classes/BiblioHoldQuery.php");
require_once("../classes/MemberQuery.php");
$bibid = $_GET["bibid"];
$copyid = $_GET["copyid"];
$mbcode = $_GET["roll_no"];

$mbrQ = new MemberQuery ();
$mbr = $mbrQ->maybeGetByBarcode($mbcode);
if (!$mbr) {
  header("Location: anon_hold.php?roll_no=".$mbcode."&msg=Bad Member Barcode");
  exit();
}
$mbrid = $mbr->getMbrid();
$copyQ = new BiblioCopyQuery ();
$copyQ->connect ();
$copy = $copyQ->doQuery($bibid, $copyid);
if (!$copy) {
  $copyQ->close();
  header("Location: anon_hold.php?roll_no=".$mbcode."&msg=Bad Book Barcode");
  exit();
} else if (!is_a($copy, 'BiblioCopy')) {
  header("Location: anon_hold.php?roll_no=".$mbcode."&msg=Unknown Error Occurred");
  exit();
} else if ($copy->getStatusCd() == OBIB_STATUS_OUT and $copy->getMbrid() == $mbrid) {
  header("Location: anon_hold.php?roll_no=".$mbcode."&msg=Member has already issued the book");
  exit();
}
$holdQ = new BiblioHoldQuery ();
$holdQ->connect ();
$holdQ->insert($mbrid,$copy->getBarcodeNmbr());
$bibQ = new BiblioQuery ();
$bib = $bibQ->doQuery($bibid, $tab);
echo "The book \"".H($bib->getBiblioFields()["245a"]->getFieldData())."\" is put on hold for ".$mbr->getFirstName()." ".$mbr->getLastName().".";
 
?>
