<?php
use CRM_Marketingdashboard_ExtensionUtil as E;

class CRM_Marketingdashboard_Page_UnsubscribeReason extends CRM_Core_Page {
  private $showAll;

  public function run() {
    try {
      CRM_Utils_System::setTitle('Optout redenen');

      $this->showAll = $this->getFilterShowAllFromUrl();

      $filterLinks = $this->getFilterLinks();
      $reasonSummary = $this->getUnsubscribeReasonSummary();
      $reasons = $this->getUnsubscribeReasons();

      $this->assign('filterLinks', $filterLinks);
      $this->assign('reasonSummary', $reasonSummary);
      $this->assign('reasons', $reasons);
    }
    catch (Exception $e) {
      CRM_Core_Session::setStatus($e->getMessage(), '', 'no-popup');
    }

    parent::run();
  }

  private function getFilterShowAllFromUrl() {
    $v = $this->getQueryStringParameter('filtered', 'Integer', FALSE);
    if ($v && $v == 1) {
      return FALSE;
    }
    else {
      return TRUE;
    }
  }

  private function getFilterLinks() {
    if ($this->showAll) {
      $queryString = 'reset=1&filtered=1';
      $url = CRM_Utils_System::url('civicrm/marketing-dashboard-unsub', $queryString);
      $filter = "Toon alle redenen | <a href=\"$url\">Toon enkel de onverwerkte</a>";
    }
    else {
      $queryString = 'reset=1';
      $url = CRM_Utils_System::url('civicrm/marketing-dashboard-unsub', $queryString);
      $filter = "<a href=\"$url\">Toon alle redenen</a> | Toon enkel de onverwerkte";
    }

    return $filter;
  }

  private function getQueryStringParameter($name, $type, $abort) {
    $v = CRM_Utils_Request::retrieve($name, $type, $this, $abort);
    return $v;
  }

  private function getUnsubscribeReasonSummary() {
    $sql = "
      select
        v.label reason
        , count(oo.id) num_reasons
      from
        civicrm_value_temporary_set_21 oo
      inner join
        civicrm_option_value v on oo.optout_reden_152 = v.value and v.option_group_id = 147
      where
        oo.optout_reden_152 is not null
      group by
        v.label
    ";

    $dao = CRM_Core_DAO::executeQuery($sql);
    $rows = [];
    while ($dao->fetch()) {
      $row = [];
      $row['reason'] = $dao->reason;
      $row['num_reasons'] = $dao->num_reasons;
      $rows[] = $row;
    }

    return $rows;
  }

  private function getUnsubscribeReasons() {
    $filter = $this->getReasonWhereClause();

    $sql = "
      select
        c.id,
        c.display_name,
        ur.optout_opmerking_153 reason
      from
        civicrm_contact c
      inner join
        civicrm_value_temporary_set_21 ur on ur.entity_id = c.id
      where
        c.is_deleted = 0
      and
        $filter
    ";

    $dao = CRM_Core_DAO::executeQuery($sql);
    $rows = [];
    while ($dao->fetch()) {
      $row = [];
      $row['display_name'] = $this->getLinkToContactSummary($dao->display_name, $dao->id);
      $row['reason'] = $dao->reason;
      $rows[] = $row;
    }

    return $rows;
  }

  private function getLinkToContactSummary($name, $id) {
    $queryString = "reset=1&cid=$id";
    $url = CRM_Utils_System::url('civicrm/contact/view', $queryString);
    $a = "<a href=\"$url\">" . $name . '</a>';

    return $a;
  }

  private function getReasonWhereClause() {
    if ($this->showAll) {
      $whereClause = "ifnull(ur.optout_opmerking_153, '') <> ''";
    }
    else {
      $whereClause = "length(ur.optout_opmerking_153) > 0 and ur.optout_opmerking_153 not like 'VERWERKT%'";
    }

    return $whereClause;
  }

}
