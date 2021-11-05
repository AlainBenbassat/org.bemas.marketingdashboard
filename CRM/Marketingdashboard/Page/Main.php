<?php
use CRM_Marketingdashboard_ExtensionUtil as E;

class CRM_Marketingdashboard_Page_Main extends CRM_Core_Page {

  public function run() {
    CRM_Utils_System::setTitle('Marketing dashboard');

    parent::run();
  }

}
