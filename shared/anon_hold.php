<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
require_once("../shared/common.php");
require_once("../functions/inputFuncs.php");
session_cache_limiter(null);

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
$nav = "home";
$helpPage = "opac";
require_once("../classes/Localize.php");
$loc = new Localize(OBIB_LOCALE,$tab);
$bibid = $_GET["bibid"];
$copyid = $_GET["copyid"];

$lookup = "N";
if (isset($_GET["lookup"])) {
  $lookup = "Y";
  $helpPage = "opacLookup";
}
if (isset($_GET["msg"])) {
  $msg = "<font class=\"error\">".H($_GET["msg"])."</font><br><br>";
} else {
  $msg = "";
}
require_once("../shared/header_opac.php");
?>

<h1>Enter Roll No as in Icard</h1>
<h3>For example if roll no is Y9XYZ then enter SY9XYZ00</h3>
<form name="enter_rollno" method="get" action="../shared/process_hold.php">
  <br />
  <table class="primary">
  <tr>
  <th valign="top" nowrap="yes" align="left">
  enter roll no as in Icard
  </td>
  </tr>
  <tr>
  <td nowrap="true" class="primary">
  <input type="hidden" name="bibid" value="<?php echo $bibid; ?>">
  <input type="hidden" name="copyid" value="<?php echo $copyid; ?>">
  <?php printInputText("roll_no",18,18,$postVars,$pageErrors); ?>
  <input type="hidden" name="tab" value="<?php echo H($tab); ?>">
  <input type="submit" value="Submit" class="button">
  </tr>
  </table>
  </form>
  <?php echo $msg ?>
  <script type="text/javascript" language="JavaScript">
  document.forms['enter_rollno'].elements['roll_no'].focus();
  </script>
  <?php include("../shared/footer.php"); ?>
