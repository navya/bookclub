<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../classes/Localize.php");
  $navLoc = new Localize(OBIB_LOCALE,"navbars");
?>

<?php if ($nav == "search") { ?>
 &raquo; <?php echo $navLoc->getText("catalogResults"); ?><br>
<?php } ?>

<?php if ($nav == "account") { ?>
 &nbsp; &nbsp; <a href="../member_circ/mbr_view.php?mbrid=<?php echo HURL($mbrid);?>" class="alt1"><?php echo $navLoc->getText("memberInfo"); ?></a><br> 
 &nbsp; &raquo; <?php echo $navLoc->getText("account"); ?></a><br>
 &nbsp; &nbsp; <a href="../member_circ/mbr_history.php?mbrid=<?php echo HURL($mbrid);?>" class="alt1"><?php echo $navLoc->getText("checkoutHistory"); ?></a><br>
<?php } ?>

<?php if ($nav == "hist") { ?>
 &nbsp; &nbsp; <a href="../member_circ/mbr_view.php?mbrid=<?php echo HURL($mbrid);?>" class="alt1"><?php echo $navLoc->getText("memberInfo"); ?></a><br>
  &nbsp; &nbsp; <a href="../member_circ/mbr_account.php?mbrid=<?php echo HURL($mbrid);?>&amp;reset=Y" class="alt1"><?php echo $navLoc->getText("account"); ?></a><br>
 &nbsp; &raquo; <?php echo $navLoc->getText("checkoutHistory"); ?></a><br> 
<?php } ?>

<?php if ($nav == "mbrview") { ?>
 &nbsp; &raquo; <?php echo $navLoc->getText("memberInfo"); ?><br>
 &nbsp; &nbsp; <a href="../member_circ/mbr_account.php?mbrid=<?php echo HURL($mbrid);?>&amp;reset=Y" class="alt1"><?php echo $navLoc->getText("account"); ?></a><br>
 &nbsp; &nbsp; <a href="../member_circ/mbr_history.php?mbrid=<?php echo HURL($mbrid);?>" class="alt1"><?php echo $navLoc->getText("checkoutHistory"); ?></a><br>
<?php } ?>


<?php if ($nav == "view") { ?>
 &nbsp; &nbsp; <a href="../member_circ/mbr_view.php?mbrid=<?php echo HURL($mbrid);?>" class="alt1"><?php echo $navLoc->getText("memberInfo"); ?></a><br>
 &nbsp; &nbsp; <a href="../member_circ/mbr_account.php?mbrid=<?php echo HURL($mbrid);?>&amp;reset=Y" class="alt1"><?php echo $navLoc->getText("account"); ?></a><br>
 &nbsp; &nbsp; <a href="../member_circ/mbr_history.php?mbrid=<?php echo HURL($mbrid);?>" class="alt1"><?php echo $navLoc->getText("checkoutHistory"); ?></a><br>
 &nbsp; &raquo; <?php echo $navLoc->getText("catalogBibInfo"); ?><br>
<?php } ?>

 <a href="../opac/index.php<?php if ($lookup == 'Y') echo "?lookup=Y"; ?> " class="alt1"><?php echo $navLoc->getText("catalogSearch2"); ?></a><br>

<a href="javascript:popSecondary('../shared/help.php<?php if (isset($helpPage)) echo "?page=".H(addslashes(U($helpPage))); ?>')"><?php echo $navLoc->getText("Help"); ?></a>
