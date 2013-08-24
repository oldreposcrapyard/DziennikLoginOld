<?php

namespace DziennikLogin\classes\reportGenerator;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of reportGenerator
 *
 * @author marcin
 */
abstract class reportGenerator {

    public $reportType;
    public $userId;
    public $reportContent;
    public $reportData;
    public $reportTo;

    public abstract function getDataToReport();

    public abstract function generateReport();

    public function getReportContent() {
        return $this->reportContent;
    }

}

?>
