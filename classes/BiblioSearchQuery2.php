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

require_once("../shared/global_constants.php");
require_once("../classes/Query.php");
require_once("../classes/BiblioField.php");
require_once("../classes/Localize.php");

/******************************************************************************
 * BiblioQuery data access component for library bibliographies
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class BiblioSearchQuery2 extends Query {
  var $_itemsPerPage = 1;
  var $_rowNmbr = 0;
  var $_currentRowNmbr = 0;
  var $_currentPageNmbr = 0;
  var $_rowCount = 0;
  var $_pageCount = 0;
  var $_loc;

  function BiblioSearchQuery2 () {
    $this->_loc = new Localize(OBIB_LOCALE,"classes");
  }
  function setItemsPerPage($value) {
    $this->_itemsPerPage = $value;
  }
  function getLineNmbr() {
    return $this->_rowNmbr;
  }
  function getCurrentRowNmbr() {
    return $this->_currentRowNmbr;
  }
  function getRowCount() {
    return $this->_rowCount;
  }
  function getPageCount() {
    return $this->_pageCount;
  }

  /****************************************************************************
   * Executes a query
   * @param string $type one of the global constants
   *               OBIB_SEARCH_BARCODE,
   *               OBIB_SEARCH_TITLE,
   *               OBIB_SEARCH_AUTHOR,
   *               or OBIB_SEARCH_SUBJECT
   * @param string @$words pointer to an array containing words to search for
   * @param integer $page What page should be returned if results are more than one page
   * @param string $sortBy column name to sort by.  Can be title or author
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function search($type, &$words, $page, $sortBy, $opacFlg=true, $collecs,$materials) {
    # reset stats
    $this->_rowNmbr = 0;
    $this->_currentRowNmbr = 0;
    $this->_currentPageNmbr = $page;
    $this->_rowCount = 0;
    $this->_pageCount = 0;

    # setting sql join clause
    $join = "from biblio left join biblio_copy on biblio.bibid=biblio_copy.bibid ";

    # setting sql where clause
    $criteria = "";
    if ((sizeof($words) == 0) || ($words[0] == "")) {
      if ($opacFlg) $criteria = "where opac_flg = 'Y' ";
      if ($collecs) $collec_criteria = $this->_getCollecCriteria("biblio.collection_cd",$collecs);
   if ($materials) $material_criteria = $this->_getMaterialCriteria("biblio.material_cd",$materials);
	  } else {
      if ($type == OBIB_SEARCH_BARCODE) {
        $criteria = $this->_getCriteria(array("biblio_copy.barcode_nmbr"),$words);
      } elseif ($type == OBIB_SEARCH_AUTHOR) {
        $criteria = $this->_getCriteria(array("biblio.author","biblio.responsibility_stmt"),$words);
      } elseif ($type == OBIB_SEARCH_SUBJECT) {
        $criteria = $this->_getCriteria(array("biblio.topic1","biblio.topic2","biblio.topic3","biblio.topic4","biblio.topic5"),$words);
      } elseif ($type == OBIB_SEARCH_ALL) {
        $criteria = $this->_getCriteria(array("biblio.topic1","biblio.topic2","biblio.topic3","biblio.topic4","biblio.topic5","biblio.title","biblio.title_remainder","biblio.author","biblio.responsibility_stmt"),$words);
      } else {
        $criteria = $this->_getCriteria(array("biblio.title","biblio.title_remainder"),$words);
      }
      if ($opacFlg) $criteria = $criteria."and opac_flg = 'Y' ";
      if ($collecs) $collec_criteria = $this->_getCollecCriteria("biblio.collection_cd",$collecs);
    if ($materials) $material_criteria = $this->_getMaterialCriteria("biblio.material_cd",$materials);
		}

    # setting count query
    $sqlcount = "select count(*) as rowcount ";
    $sqlcount = $sqlcount.$join;
    $sqlcount = $sqlcount.$criteria;
    if(isset($collec_criteria)){
    $sqlcount = $sqlcount.$collec_criteria; }
    if(isset($material_criteria)){
    $sqlcount = $sqlcount.$material_criteria;  }
    # setting query that will return all the data
    $sql = "select biblio.* ";
    $sql = $sql.",biblio_copy.copyid ";
    $sql = $sql.",biblio_copy.barcode_nmbr ";
    $sql = $sql.",biblio_copy.status_cd ";
    $sql = $sql.",biblio_copy.due_back_dt ";
    $sql = $sql.",biblio_copy.mbrid ";
    $sql = $sql.$join;
    $sql = $sql.$criteria;
    if(isset($collec_criteria)){
    $sql = $sql.$collec_criteria; }
    if(isset($material_criteria)){
    $sql = $sql.$material_criteria; }
    $sql = $sql." order by ".$sortBy;

    # setting limit so we can page through the results
    $offset = ($page - 1) * $this->_itemsPerPage;
    $limit = $this->_itemsPerPage;
    $sql = $sql." limit ".$offset.",".$limit;

    //echo "sqlcount=[".$sqlcount."]<br>\n";
    //exit("sql=[".$sql."]<br>\n");

    # Running row count sql statement
    $countResult = $this->_conn->exec($sqlcount);
    if ($countResult == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioSearchQueryErr1");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }

    # Calculate stats based on row count
    $array = $this->_conn->fetchRow();
    $this->_rowCount = $array["rowcount"];
    $this->_pageCount = ceil($this->_rowCount / $this->_itemsPerPage);

    # Running search sql statement
    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioSearchQueryErr2");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }

    return $result;
  }


  /****************************************************************************
   * Utility function to get the selection criteria for a given column and set of values
   * @param string $col bibid of bibliography to select
   * @param array reference &$words array of words to search for
   * @return string returns SQL criteria syntax for the given column and set of values
   * @access private
   ****************************************************************************
   */
  function _getCriteria($cols,&$words) {
    # setting selection criteria sql
    $prefix = "where ";
    $criteria = "";
    for ($i = 0; $i < count($words); $i++) {
      $criteria = $criteria.$prefix.$this->_getLike($cols,$words[$i]);
      $prefix = " and ";
    }
    return $criteria;
  }

  function _getLike(&$cols,$word) {
    $prefix = "";
    $suffix = "";
    if (count($cols) > 1) {
      $prefix = "(";
      $suffix = ")";
    }
    $like = "";
    for ($i = 0; $i < count($cols); $i++) {
      $like = $like.$prefix.$cols[$i]." like '%".$word."%'";
      $prefix = " or ";
    }
    $like = $like.$suffix;
    return $like;
  }

  function _getCollecCriteria($col,$codes) {
    # setting additional selection criteria sql
     $prefix = "and (";
     $collec_criteria =  "";
     for ($i = 0; $i < count($codes); $i++) {
       $collec_criteria = $collec_criteria.$prefix.$col."=".$codes[$i];
       $prefix = " or ";
       }
     $collec_criteria = $collec_criteria.")";
    return $collec_criteria;
    }
  function _getMaterialCriteria($col,$codes) {
    # setting additional selection criteria sql
     $prefix = "and (";
     $material_criteria =  "";
     for ($j = 0; $j < count($codes); $j++) {
       $material_criteria = $material_criteria.$prefix.$col."=".$codes[$j];
       $prefix = " or ";
       }
     $material_criteria = $material_criteria.")";
    return $material_criteria;
    }
  /****************************************************************************
   * Executes a query to select ONLY ONE SUBFIELD
   * @param string $bibid bibid of bibliography copy to select
   * @param string $fieldid copyid of bibliography copy to select
   * @return BiblioField returns subfield or false, if error occurs
   * @access public
   ****************************************************************************
   */
  function query($statusCd,$mbrid="") {
    
    $sql = "select biblio.* ";
    $sql = $sql.",biblio_copy.copyid ";
    $sql = $sql.",biblio_copy.barcode_nmbr ";
    $sql = $sql.",biblio_copy.status_cd ";
    $sql = $sql.",biblio_copy.status_begin_dt ";
    $sql = $sql.",biblio_copy.due_back_dt ";
    $sql = $sql.",biblio_copy.mbrid ";
    $sql = $sql.",greatest(0,to_days(sysdate()) - to_days(biblio_copy.due_back_dt)) days_late ";
    $sql = $sql."from biblio, biblio_copy ";
    $sql = $sql."where biblio.bibid = biblio_copy.bibid ";
    if ($mbrid != "") {
        $sql = $sql."and biblio_copy.mbrid = ".$mbrid;
    }
    $sql = $sql." and biblio_copy.status_cd = '".$statusCd."'";

    $result = $this->_conn->exec($sql);
    if ($result == false) {
      $this->_errorOccurred = true;
      $this->_error = $this->_loc->getText("biblioSearchQueryErr3");
      $this->_dbErrno = $this->_conn->getDbErrno();
      $this->_dbError = $this->_conn->getDbError();
      $this->_SQL = $sql;
      return false;
    }
    $this->_rowCount = $this->_conn->numRows();
    return $result;
  }

  /****************************************************************************
   * Fetches a row from the query result and populates the BiblioSearch object.
   * @return BiblioSearch returns bibliography search record or false if no more bibliographies to fetch
   * @access public
   ****************************************************************************
   */
  function fetchRow() {
    $array = $this->_conn->fetchRow();
    if ($array == false) {
      return false;
    }

    # increment rowNmbr
    $this->_rowNmbr = $this->_rowNmbr + 1;
    $this->_currentRowNmbr = $this->_rowNmbr + (($this->_currentPageNmbr - 1) * $this->_itemsPerPage);

    $bib = new BiblioSearch();
    $bib->setBibid($array["bibid"]);
    $bib->setCopyid($array["copyid"]);
    $bib->setCreateDt($array["create_dt"]);
    $bib->setLastChangeDt($array["last_change_dt"]);
    $bib->setLastChangeUserid($array["last_change_userid"]);
    $bib->setMaterialCd($array["material_cd"]);
    $bib->setCollectionCd($array["collection_cd"]);
    $bib->setCallNmbr1($array["call_nmbr1"]);
    $bib->setCallNmbr2($array["call_nmbr2"]);
    $bib->setCallNmbr3($array["call_nmbr3"]);
    $bib->setTitle($array["title"]);
    $bib->setTitleRemainder($array["title_remainder"]);
    $bib->setResponsibilityStmt($array["responsibility_stmt"]);
    $bib->setAuthor($array["author"]);
    $bib->setTopic1($array["topic1"]);
    $bib->setTopic2($array["topic2"]);
    $bib->setTopic3($array["topic3"]);
    $bib->setTopic3($array["topic4"]);
    $bib->setTopic3($array["topic5"]);
    if (isset($array["barcode_nmbr"])) {
      $bib->setBarcodeNmbr($array["barcode_nmbr"]);
    }
    if (isset($array["status_cd"])) {
      $bib->setStatusCd($array["status_cd"]);
    }
    if (isset($array["status_begin_dt"])) {
      $bib->setStatusBeginDt($array["status_begin_dt"]);
    }
    if (isset($array["status_mbrid"])) {
      $bib->setStatusMbrid($array["status_mbrid"]);
    }
    if (isset($array["due_back_dt"])) {
      $bib->setDueBackDt($array["due_back_dt"]);
    }
    if (isset($array["days_late"])) {
      $bib->setDaysLate($array["days_late"]);
    }

    return $bib;
  }


}

?>
