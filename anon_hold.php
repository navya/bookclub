<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  session_cache_limiter(null);

  $tab = "opac";
  $nav = "home";
  $helpPage = "opac";
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

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

<h1>Enter Roll No.</h1>
<form name="enterrollno" method="get" action="../shared/process_hold.php">
<br />
<table class="primary">
  <tr>
    <th valign="top" nowrap="yes" align="left">
    enter roll no
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <input type="text" name="roll_no" size="30" maxlength="256">
      <input type="hidden" name="tab" value="<?php echo H($tab); ?>">
      <input type="submit" value="Submit" class="button">
    </td>1
  </tr>
</table>
</form>
<?php echo $msg ?>

<?php include("../shared/footer.php"); ?>
