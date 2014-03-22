<?php
require_once('../classes/Lay.php');

class Layout_hello {
  function render($rpt) {
    $lay = new Lay;
      $lay->container('TextLine');
        $lay->text('Hello, World!');
      $lay->close();
    $lay->close();
  }
}

