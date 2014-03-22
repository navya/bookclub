<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
require_once("../shared/common.php");
$tab = "circulation";
$nav = "view";
$restrictInDemo = true;
require_once("../shared/logincheck.php");

require_once("../classes/BiblioCopyQuery.php");
require_once("../classes/BiblioQuery.php");
require_once("../classes/MemberQuery.php");
require_once("../classes/CircQuery.php");
require_once("../classes/Date.php");
require_once("../functions/errorFuncs.php");
require_once("../functions/formatFuncs.php");
require_once("../classes/Localize.php");
require_once "Mail.php";
require_once("../shared/mail_vars.php");
$loc = new Localize(OBIB_LOCALE,$tab);

if (count($_GET) != 0) {
  $_POST = $_GET;
}
if (count($_POST) == 0) {
  header("Location: ../circ/index.php");
  exit();
}
$barcode = trim($_POST["barcodeNmbr"]);
$mbrid = trim($_POST["mbrid"]);
$renewal = isset($_POST["renewal"]);
$action=$renewal?"renewed":"issued";
$mbrQ = new MemberQuery;
$mbr = $mbrQ->get($mbrid);
  
$postVars = $_POST;
$pageErrors = array();
  
function checkerror($field, $err) {
  global $mbrid, $postVars, $pageErrors;
  if (!$err)
    return;
  $pageErrors[$field] = $err->toStr();
  $_SESSION["postVars"] = $postVars;
  $_SESSION["pageErrors"] = $pageErrors;
  header("Location: ../circ/mbr_view.php?mbrid=".U($mbrid));
  exit();
}
  
$circQ = new CircQuery;
if(isset($_POST['date_from']) && isset($_POST['dueDate']) && $_POST['date_from'] == 'override'){
  list($dueDate, $err) = Date::read_e($_POST['dueDate']);
  checkerror('dueDate', $err);
  $_SESSION['due_date_override'] = $_POST['dueDate'];
  $err = $circQ->checkout_due_e($mbr->getBarcodeNmbr(), $barcode, $dueDate);
  checkerror('barcodeNmbr', $err);
} else {
  $err = $circQ->checkout_e($mbr->getBarcodeNmbr(), $barcode);
  checkerror('barcodeNmbr', $err);
}
$copyQ = new BiblioCopyQuery;
$bibQ = new BiblioQuery;
$copy=$copyQ->queryByBarcode($barcode);
$to=$mbr->getFirstName().' '.$mbr->getLastName()." <".$mbr->getEmail().">";
$msgBodys = "Dear ".$mbr->getFirstName().' '.$mbr->getLastName().",\n";
$biblio = $bibQ->doQuery($copy->getBibid());
$biblioFlds = $biblio->getBiblioFields();
$msgBodym= "\nYou have just ".$action." the book \"".$biblioFlds["245a"]->getFieldData()."\" by \"".$biblioFlds["100a"]->getFieldData()."\" at ".date("g:i A")." today(".date("D, d M, Y")."). The due date of the book is ".$copy->getDueBackDt().".\n";
$subject = "[Book Club] Book ".$action;
$body = $msgBodys.$msgBodym.$msgBodye;
require_once("../shared/send_mail.php");

unset($_SESSION["postVars"]);
unset($_SESSION["pageErrors"]);

header("Location: ../circ/mbr_view.php?mbrid=".U($mbrid));
?>
