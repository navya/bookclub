<?php

  require_once("../classes/Member.php");
  require_once("../classes/MemberQuery.php");
  require_once("../functions/searchFuncs.php");
  
#****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************
  if (count($_POST) == 0) {
    header("Location: ../circ/index.php");
    exit();
  }

  #****************************************************************************
  #*  Checking the Username and Password
  #****************************************************************************
  
	$ftp_server = "webhome.cc.iitk.ac.in";
	$ftp_user = $_POST["username"];
	$ftp_pass = $_POST["password"];
	$conn = ftp_connect($ftp_server) or die("could not connect");
	if (@ftp_login($conn,$ftp_user,$ftp_pass)) {
		echo "Connected as $ftp_user@$ftp_server\n";
		ftp_close($conn);
	
  

		#****************************************************************************
		#*  Loading a few domain tables into associative arrays
		  #****************************************************************************
		  /*$dmQ = new DmQuery();
		  $dmQ->connect();
		  $mbrClassifyDm = $dmQ->getAssoc("mbr_classify_dm");
		  $dmQ->close();
			*/
		  #****************************************************************************
		  #*  Retrieving post vars and scrubbing the data
		  #****************************************************************************
		  if (isset($_POST["page"])) {
			$currentPageNmbr = $_POST["page"];
		  } else {
			$currentPageNmbr = 1;
		  }
		  # remove redundant whitespace
		  $searchText = $ftp_user."@iitk.ac.in";
		  $sType = OBIB_SEARCH_EMAIL;
		  #****************************************************************************
		  #*  Search database
		  #****************************************************************************
		  $mbrQ = new MemberQuery();
		  $mbrQ->setItemsPerPage(OBIB_ITEMS_PER_PAGE);
		  $mbrQ->connect();
		  $mbrQ->execSearch($sType,$searchText,$currentPageNmbr);

		  #**************************************************************************
		  #*  Show member view screen if only one result from barcode query
		  #**************************************************************************
		  $mbr = $mbrQ->fetchMember();
		  $mbrQ->close();
			header("Location: ../member_circ/mbr_view.php?mbrid=".U($mbr->getMbrid())."&reset=Y");
			//echo "memeber id found is ".U($mbr->getFirstName());
			exit();
	} else {
		header('Location: login.html');
		exit();
	}
?>
