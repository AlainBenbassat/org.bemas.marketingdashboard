<?php
use CRM_Marketingdashboard_ExtensionUtil as E;

class CRM_Marketingdashboard_Page_CourseOrigin extends CRM_Core_Page {
  private $year;

  public function run() {
    try {
      CRM_Utils_System::setTitle('Hoe bent u bij onze opleiding terechtgekomen?');

      $this->year = $this->getFilterYearFromUrl();

      $filterLinks = $this->getFilterLinks();
      $sourceSummary = $this->getCourseOriginSummary();
      $sources = $this->getCourseOrigins();

      $this->assign('filterLinks', $filterLinks);
      $this->assign('sourceSummary', $sourceSummary);
      $this->assign('sources', $sources);
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
        $url = CRM_Utils_System::url('civicrm/marketing-dashboard-course-origin', $queryString);
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

  private function getCourseOriginSummary() {
    $sql = "
      select
        wfsd.data source,
        count(wfs.sid) num_sources
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
        wfc.type = 'select'
      and
        wfc.extra like '%collega|%'
      and
        n.title not like 'TEMPLATE%'
      and
        year(FROM_UNIXTIME(wfs.submitted)) = {$this->year}
      group by
        wfsd.data
      order by
        1
    ";
    $result = db_query($sql);

    $rows = [];
    foreach ($result as $record) {
      $row = [];
      $row['source'] = $record->source;
      $row['num_sources'] = $record->num_sources;
      $rows[] = $row;
    }

    return $rows;
  }

  private function getCourseOrigins() {
    $sql = "
      select
        n.nid,
        wfs.sid
        , FROM_UNIXTIME(wfs.submitted) answer_date
        , n.title event
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
        wfc.type = 'select'
      and
        wfc.extra like '%collega|%'
      and
        n.title not like 'TEMPLATE%'
      and
        wfsd.data = 'andere'
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
