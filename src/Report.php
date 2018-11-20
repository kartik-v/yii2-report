<?php
/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2018
 * @package yii2-report
 * @version 1.0.0
 */

namespace kartik\report;

use Yii;
use yii\base\Component;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Response;

/**
 * The Report class is a Yii2 component component to generate beautiful formatted reports
 * using Microsoft Word Document Templates in PDF/DOCX format.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class Report extends Component
{
    /**
     * PHP Reports API
     */
    const API = 'https://www.php-reports.com/api/report/generate';

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
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->validateConfig();
        $this->templateVariables = array_replace_recursive($this->defaultTemplateVariables, $this->templateVariables);
    }

    /**
     * Validate configuration pre-requisites
     *
     * @throws InvalidConfigException
     */
    protected function validateConfig()
    {
        if (!extension_loaded('curl')) {
            throw new InvalidConfigException("The 'curl' php extension must be enabled and loaded to generate reports via 'yii2-report'.");
        }
        if (empty($this->apiKey) || strlen($this->apiKey) != 24) {
            throw new InvalidConfigException("Invalid API key! Ensure a valid 'apiKey' has been configured in the 'yii2-report' component.");
        }
        if (empty($this->templateId) || !is_numeric($this->templateId)) {
            throw new InvalidConfigException("Invalid Template ID! Ensure a valid numeric 'templateId' has been configured in the 'yii2-report' component.");
        }
        if (empty($this->templateVariables) && empty($this->defaultTemplateVariables)
            || !is_array($this->templateVariables) || !is_array($this->defaultTemplateVariables)) {
            throw new InvalidConfigException("Invalid Template Variables! Template variables must be configured as an array.");
        }
        if ($this->outputAction != self::ACTION_FORCE_DOWNLOAD && $this->outputAction != self::ACTION_GET_DOWNLOAD_URL) {
            $this->outputAction = self::ACTION_FORCE_DOWNLOAD;
        }
        if ($this->outputFileType != self::OUTPUT_PDF && $this->outputFileType != self::OUTPUT_DOCX) {
            $this->outputFileType = self::OUTPUT_PDF;
        }
    }

    /**
     * Generate the report
     * @throws InvalidCallException
     * @return mixed
     */
    public function generateReport()
    {
        $postFields = [
            'api_key' => $this->apiKey,
            'template_id' => $this->templateId,
            'template_variables' => $this->templateVariables,
            'output_file_name' => $this->outputFileName,
            'output_file_type' => $this->outputFileType,
            'output_action' => $this->outputAction,
        ];
        $json = Json::encode($postFields);
        $ch = curl_init(self::API);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "json={$json}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode != 200 && $httpCode != 302) {
            throw new InvalidCallException("Can not make API request. HTTP status code: {$httpCode}");
        }
        $response = Json::decode($response);
        if (ArrayHelper::getValue($response, 'result', '') === 'error') {
            throw new InvalidCallException("Report Generation Error!" . "\n\n" . 
                ArrayHelper::getValue($response, 'error_code', '') . ': ' .
                ArrayHelper::getValue($response, 'error_message', ''));
        }
        $url = ArrayHelper::getValue($response, 'report_url', '');
        if (empty($url)) {
            throw new InvalidCallException("Could not process the API request. Invalid response URL received from the API.");
        }
        return $this->outputAction == self::ACTION_GET_DOWNLOAD_URL ? $url : Yii::$app->controller->redirect($url);
    }
}