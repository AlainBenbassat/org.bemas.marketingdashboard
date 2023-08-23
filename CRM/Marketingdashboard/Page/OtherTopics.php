<?php
use CRM_Marketingdashboard_ExtensionUtil as E;

class CRM_Marketingdashboard_Page_OtherTopics extends CRM_Core_Page {
  private $year;

  public function run() {
    try {
      CRM_Utils_System::setTitle('Over welke andere onderwerpen zou u graag eens een BEMAS-activiteit of opleiding bijwonen?');

      $this->year = $this->getFilterYearFromUrl();

      $filterLinks = $this->getFilterLinks();
      $topics = $this->getOtherTopics();

      $this->assign('filterLinks', $filterLinks);
      $this->assign('sources', $topics);
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
        $url = CRM_Utils_System::url('civicrm/marketing-dashboard-other-topics', $queryString);
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

  private function getOtherTopics() {
    $sql = "
      select
        n.nid
        , wfs.sid
        , FROM_UNIXTIME(wfs.submitted) answer_date
        , n.title event
        , wfsd.`data` topic
      from
        node n
      inner join
        webform wf on n.nid = wf.nid
      inner join
        webform_component wfc on wf.nid = wfc.nid
      inner JOIN
        webform_submissions wfs on wfs.nid = wfc.nid
      inner JOIN
        webform_submitted_data wfsd on wfs.sid = wfsd.sid and wfsd.cid = wfc.cid
      where
        n.type = 'webform'
      and
        wfc.form_key like 'evalform_q%'
      and
        wfc.type = 'textarea'
      and
        (wfc.name like '%andere onderwerpen%' or wfc.name like '%autres sujets%' or wfc.name like '%other topics%')
      and
        n.title not like 'TEMPLATE%'
      and
        LENGTH(wfsd.data) > 0
      and
        year(FROM_UNIXTIME(wfs.submitted)) = {$this->year}
      order by
        wfs.sid desc
    ";

    $result = db_query($sql);

    $rows = [];
    foreach ($result as $record) {
      $row = [];
      $row['date'] = $record->answer_date;
      $row['event'] = $record->event;
      $row['topic'] = $record->topic;
      $row['submission'] = $this->getSubmissionLink($record->nid, $record->sid);
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
