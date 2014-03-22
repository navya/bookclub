<?php
require_once('../classes/BiblioQuery.php');
require_once('../classes/BiblioCopyQuery.php');

class Layout_deleteEmpty {
  function render($rpt) {
    list($rpt, $errs) = $rpt->variant_el(array('order_by'=>'bibid'));
    if (!empty($errs)) {
      Fatal::internalError('Unexpected report error');
    }
    $copyQ = new BiblioCopyQuery;
    $bibQ = new BiblioQuery;
    while ($row = $rpt->each()) {
      if($row['copies']=='0') {
        if($copyQ->nextCopyid($row['bibid'])=='1') {
          $bibQ->delete($row['bibid']);
        }
      }
    }
  }
}
?>
