<?php
use CRM_Marketingdashboard_ExtensionUtil as E;

class CRM_Marketingdashboard_Page_MembershipEndingReason extends CRM_Core_Page {
  private $year;

  public function run() {
    try {
      CRM_Utils_System::setTitle('Redenen opzegging lidmaatschap');

      $this->year = $this->getFilterYearFromUrl();

      $filterLinks = $this->getFilterLinks();
      $reasonsSummary = $this->getMembershipEndingReasonsSummary();
      $reasons = $this->getMembershipEndings();

      $this->assign('filterLinks', $filterLinks);
      $this->assign('reasonsSummary', $reasonsSummary);
      $this->assign('reasons', $reasons);
    }
    catch (Exception $e) {
      CRM_Core_Session::setStatus($e->getMessage(), '', 'no-popup');
    }

    parent::run();
  }

  private function getFilterLinks() {
    $numYears = 3;
    $currentYear = date('Y');
    $filter = '';

    for ($y = $currentYear - $numYears; $y <= $currentYear; $y++) {
      if ($filter) {
        $filter .= ' | ';
      }

      if ($y == $this->year) {
        $filter .= $y;
      }
      else {
        $queryString = "reset=1&year=$y";
        $url = CRM_Utils_System::url('civicrm/marketing-dashboard-membership-ending-reason', $queryString);
        $filter .= "<a href=\"$url\">$y</a>";
      }
    }

    return $filter;
  }

  private function getFilterYearFromUrl() {
    $v = $this->getQueryStringParameter('year', 'Integer', FALSE);
    if ($v && strlen($v) == 4) {
      return $v;
    }
    else {
      return date('Y');
    }
  }

  private function getQueryStringParameter($name, $type, $abort) {
    $v = CRM_Utils_Request::retrieve($name, $type, $this, $abort);
    return $v;
  }

  private function getMembershipEndingReasonsSummary() {
    $sql = "
      select
        ov.label reason,
        count(*) num_reasons
      from
        civicrm_contact c
      inner join
        civicrm_membership m on c.id = m.contact_id
      inner join
        civicrm_value_lidmaatschap__35 md on m.id = md.entity_id
      inner join
        civicrm_option_value ov on md.reason_end_membership_165 = ov.value and ov.option_group_id = 150
      where
        m.owner_membership_id is null
      and
        year(m.end_date) = {$this->year}
      and
        c.is_deleted = 0
      group by
        md.reason_end_membership_165
      order by
        ov.value
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

  private function getMembershipEndings() {
    $sql = "
      select
        c.display_name,
        ov.label reason
      from
        civicrm_contact c
      inner join
        civicrm_membership m on c.id = m.contact_id
      inner join
        civicrm_value_lidmaatschap__35 md on m.id = md.entity_id
      inner join
        civicrm_option_value ov on md.reason_end_membership_165 = ov.value and ov.option_group_id = 150
      where
        m.owner_membership_id is null
      and
        year(m.end_date) = {$this->year}
      and
        c.is_deleted = 0
      and
        md.reason_end_membership_165 is not null
      order by
        c.sort_name
    ";

    $dao = CRM_Core_DAO::executeQuery($sql);
    $rows = [];
    while ($dao->fetch()) {
      $row = [];
      $row['contact'] = $dao->display_name;
      $row['reason'] = $dao->reason;
      $rows[] = $row;
    }

    return $rows;
  }

  private function getSubmissionLink($nid, $sid) {
    $link = CRM_Utils_System::baseURL() . "node/$nid/submission/$sid";
    $link = '<a href="' . $link . '">details</a>';

    return $link;
  }

}
