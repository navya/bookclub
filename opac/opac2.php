<?php
/**********************************************************************************
 *   Copyright(C) 2002 David Stevens, 2004 Hans van der Weij (opac search collection limit, OBIB_SEARCH_ALL)
 *
 *   This file is part of OpenBiblio.
 *
 *   OpenBiblio is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   OpenBiblio is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with OpenBiblio; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 **********************************************************************************
 */

  $tab = "opac";
  $nav = "home";
  $focus_form_name = "phrasesearch";
  $focus_form_field = "searchText";

  require_once("../shared/read_settings.php");
  require_once("../shared/header_opac.php");
  require_once("../functions/inputFuncs.php");
require_once("../classes/Localize.php");

  $loc = new Localize(OBIB_LOCALE,"opac");
  $lookup = "N";
  if (isset($HTTP_GET_VARS["lookup"])) {
    $lookup = "Y";
  }
?>

<h1>Online Public Access Catalog (OPAC)</h1>
Welcome to our library's oline public access catalog.  Search our catalog
to view bibliography information on holdings we have in our library.
<form name="phrasesearch" method="POST" action="../shared/biblio_search2.php">
<br />
<table class="primary">
  <tr>
    <th valign="top" nowrap="yes" align="left">
      Search Bibliography by Search Phrase:
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <select name="searchType">
        <option value="title">Title
        <option value="author">Author
        <option value="subject">Subject
        <option value="all" selected>Title+Author+Subject
      </select>
      <input type="text" name="searchText" size="30" maxlength="256">
      <input type="hidden" name="sortBy" value="default">
      <input type="hidden" name="tab" value="<?php echo $tab; ?>">
      <input type="hidden" name="lookup" value="<?php echo $lookup; ?>">
      <input type="submit" value="Search" class="button">
    </td>
  </tr>
</table>
<br>

<table class="primary">
  <tr><th valign="top" nowrap="yes" align="left">Limit Search to Collections:</td>
   <th valign="top" nowrap="yes" align="left">Limit Search to Material Types:</td>
  </tr>
  <tr>
    <font class="small">
    <td nowrap="true" class="primary">
<script language="JavaScript"> 
function selectAll(obj) { 
var checkBoxes = document.getElementsByTagName('input'); 
for (i = 0; i < checkBoxes.length; i++) { 
if (obj.checked == true) { 
checkBoxes[i].checked = true; // this checks all the boxes 
} else { 
checkBoxes[i].checked = false; // this unchecks all the boxes 
} 
} 
}
</script>
<input type="checkbox" name="selectall" value="select_all" onclick="selectAll(this);" checked><b>Select/Unselect All</b><br>
     <?php
      $dmQ = new DmQuery();
      $dmQ->connect();
      if ($dmQ->errorOccurred()) {
        $dmQ->close();
        displayErrorPage($dmQ);
      }
      $dmQ->execSelectWithStats('collection_dm');
      if ($dmQ->errorOccurred()) {
        $dmQ->close();
        displayErrorPage($dmQ);
      }
      $colleccount=0;
     while ($dm = $dmQ->fetchRow()) {
    
        if ($dm->getCount() > 0) { ?>
        
          <input type="checkbox" name="collec=<?php echo $dm->getCode();?>" value="collec" checked> <?php echo $dm->getDescription();?><br>
          <?php $colleccount= $colleccount + 1;
        }
      }
     $dmQ->close();
   ?>
   <input type="hidden" name="colleccount" value="<?php echo $colleccount; ?>">
    </td>

    <td nowrap="true" valign="top" class="primary">

    <?php
      $dmQ = new DmQuery();
      $dmQ->connect();
      if ($dmQ->errorOccurred()) {
        $dmQ->close();
        displayErrorPage($dmQ);
      }
      $dmQ->execSelectWithStats('material_type_dm');
      if ($dmQ->errorOccurred()) {
        $dmQ->close();
        displayErrorPage($dmQ);
      }
      $matcount=0;
     while ($dm = $dmQ->fetchRow()) {
    
        if ($dm->getCount() > 0) { ?>
          <input type="checkbox" name="material=<?php echo $dm->getCode();?>" value="material" checked> <?php echo $dm->getDescription();?><br>
          <?php $matcount= $matcount + 1;
        }
      }
     $dmQ->close();
   ?>
   <input type="hidden" name="matcount" value="<?php echo $matcount; ?>">
</td> </tr>
</font>
</table>
</form>


<?php include("../shared/footer.php"); ?>
