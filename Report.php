<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2018
 * @package yii2-report
 * @version 1.0.0
 */

namespace kartik\report;

use PHPReports\PHPReports;

/**
 * The Report class is a Yii2 component component to generate beautiful formatted reports 
 * using Microsoft Word Document Templates in PDF/DOC/DOCX format. It uses the [[PhpReports]]
 * library.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class Report extends Component
{
    /**
     * Output File Type - MS Word DOCX
     */
    const OUTPUT_DOCX = 1;

    /**
     * Output File Type - PDF
     */
    const OUTPUT_PDF = 2;

    /**
     * Output File Action - get download URL as JSON
     */
    const ACTION_GET_DOWNLOAD_URL = 1;

    /**
     * Output File Action - force download generated report
     */
    const ACTION_FORCE_DOWNLOAD = 2;

    /**
     * @var string specifies the api key for php-reports library
     */
    public $apiKey;

    /**
     * @var integer specifies the template identifier
     */
    public $templateId;
    
    /**
     * @var array default template variables that will be merged with [[templateVariables]]
     */
    public $defaultTemplateVariables = [];
    
    /**
     * @var array template variables as key value pairs to be replaced within the 
     * MS Word Document Template
     */
    public $templateVariables = [];

    /**
     * @var string the output file name
     */
    public $outputFileName;

    /**
     * @var string the output file type
     */
    public $outputFileType;

    /**
     * @var string the output action
     */
    public $outputAction;
    
    /**
     * @var PhpReports the php report object
     */
    protected $_report;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->initReport();
        parent::init();
    }

    /**
     * Initialize report library
     *
     * @throws InvalidConfigException
     */
    protected function initReport()
    {
        $vars = $this->templateVariables;
        $vars = array_replace_recursive($this->defaultTemplateVariables, $vars);
        $r = new PHPReports($this->apiKey);
        $r->setTemplateId($this->templateId);
        $r->setOutputAction($this->outputAction);
        $r->setOutputFileType($this->outputFileType);
        $r->setOutputFileName($this->outputFileName);
        $r->setTemplateVariables($vars);
        $this->_report = $r;
    }
    
    /**
     * Gets the PHPReports report object instance
     *
     * @return PHPReports
     */
    public function getReport()
    {
        return $this->_report;
    }
    
    /**
     * Generate the report
     */
    public function generateReport()
    {
        $this->_report->generateReport();
    }
}
