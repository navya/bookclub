<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
    require_once("../classes/BiblioQuery.php");
    require_once("../classes/Biblio.php");
    require_once("../classes/BiblioField.php");
 
  if (count($_GET) == 0) {
    header("Location: ../catalog/index.php");
    exit();
  }
  
  $bibid = $_GET["bibid"];
  $newisbn = $_GET['newisbn'];
  $biblioQ = new BiblioQuery();
  $biblioQ->connect();
  if ($biblioQ->errorOccurred()) {
    $biblioQ->close();
    displayErrorPage($biblioQ);
  }
  if (!$biblio = $biblioQ->doQuery($bibid)) {
    $biblioQ->close();
    displayErrorPage($biblioQ);
  }
  $bibFld = new BiblioField();
  $bibFld->setBibid($bibid);
  if(array_key_exists('020a', $biblio->getBiblioFields())) {
    print_r($biblio->getBiblioFields());
    print_r($biblio->getBiblioFields()['020a']);
    print_r($biblio->getBiblioFields()['020a']->getFieldid());
    $bibFld->setFieldid($biblio->getBiblioFields()['020a']->getFieldid());
  }
  else {
    $bibFld->setFieldid('');  	
  }
  $bibFld->setTag(20);
  $bibFld->setInd1Cd('N');
  $bibFld->setInd2Cd('N');
  $bibFld->setSubfieldCd('a');
  $bibFld->setFieldData($newisbn);
  $biblio->addBiblioField('020a', $bibFld);
  $biblioQ->update($biblio);
  if (!$biblio = $biblioQ->doQuery($bibid)) {
    $biblioQ->close();
    displayErrorPage($biblioQ);
  }
  header("Location: ../shared/biblio_view.php?bibid=$bibid");
?>
  
