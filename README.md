yii2-report
===========

[![Latest Stable Version](https://poser.pugx.org/kartik-v/yii2-report/v/stable)](https://packagist.org/packages/kartik-v/yii2-report)
[![Latest Unstable Version](https://poser.pugx.org/kartik-v/yii2-report/v/unstable)](https://packagist.org/packages/kartik-v/yii2-report)
[![License](https://poser.pugx.org/kartik-v/yii2-report/license)](https://packagist.org/packages/kartik-v/yii2-report)
[![Total Downloads](https://poser.pugx.org/kartik-v/yii2-report/downloads)](https://packagist.org/packages/kartik-v/yii2-report)
[![Monthly Downloads](https://poser.pugx.org/kartik-v/yii2-report/d/monthly)](https://packagist.org/packages/kartik-v/yii2-report)
[![Daily Downloads](https://poser.pugx.org/kartik-v/yii2-report/d/daily)](https://packagist.org/packages/kartik-v/yii2-report)

A Yii2 component to generate beautiful formatted reports using Microsoft Word Document Templates in PDF/DOC/DOCX format. The component uses the [PHP reports library API](https://www.php-reports.com/) to generate reports. PHP-Reports is a cloud based, interactive report engine which helps in generating well formatted PDF reports from Word / DOCX templates.

How to contribute via a pull request?
-------------------------------------
Refer this [git workflow for contributors](.github/GIT-WORKFLOW.md).

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

## Pre-requisites

> Note: Check the [composer.json](https://github.com/kartik-v/yii2-dropdown-x/blob/master/composer.json) for this extension's requirements and dependencies. 
You must set the `minimum-stability` to `dev` in the **composer.json** file in your application root folder before installation of this extension OR
if your `minimum-stability` is set to any other value other than `dev`, then set the following in the require section of your composer.json file

```
kartik-v/yii2-report: "@dev"
```

Read this [web tip /wiki](http://webtips.krajee.com/setting-composer-minimum-stability-application/) on setting the `minimum-stability` settings for your application's composer.json.

## Install

Either run

```
$ php composer.phar require kartik-v/yii2-report "@dev"
```

or add

```
"kartik-v/yii2-report": "@dev"
```

to the ```require``` section of your `composer.json` file.

## Usage

### Step 1: Getting API Key 

Create free account at https://www.php-reports.com and get your free API Key. 

### Step 2: Creating Your First Template

To accelerate the process of creating your reports and applications, PHP Reports takes advantage of Microsoft Word's design capabilites. Simply create a Microsoft Word file and design your report.

### Step 3: Using Template Variables

You can use template variables in your Microsoft Word template. Tamplate variables take their name from the contents of their double curly braces and they can later be replaced with a concrete value.

All template variable names within a template string must be unique. Template variable names are case-insensitive.

![Word Template Screenshot](https://www.php-reports.com/images/php-reports-variables.png)

### Step 4: Upload Your Template

Save and upload your template at "Template Manager" section in https://www.php-reports.com. Remember the template identifier (`templateId`).

### Step 5: Setting up the `yii2-report` global component

Setup the following component in the `components` section of your Yii2 application configuration file.

```php
use kartik\report\Report;

// ...
'components' => [
    // setup Krajee Pdf component
    'report' => [
        'class' => Report::classname(),
        'apiKey' => 'YOUR_PHP_REPORTS_API_KEY',
        // the following variables can be set to globally default your settings
        'templateId' => 1, // optional: the numeric identifier for your default global template 
        'outputAction' => Report::ACTION_FORCE_DOWNLOAD, // or Report::ACTION_GET_DOWNLOAD_URL 
        'outputFileType' => Report::OUTPUT_PDF, // or Report::OUTPUT_DOCX
        'outputFileName' => 'KrajeeReport.pdf', // a default file name if 
        'defaultTemplateVariables' => [ // any default data you desire to always default
            'companyName' => 'Krajee.com'
        ]
    ]
]
```

### Step 6: Generating the report from the component

```php
use kartik\report\Report;

$report = Yii::$app->report;

// set your template identifier (override global defaults)
$report->templateId = 2;

// If you want to override the output file name, uncomment line below
// $report->outputFileName = 'My_Generated_Report.pdf';

// If you want to override the output file type, uncomment line below
// $report->outputFileType = Report::OUTPUT_DOCX;

// If you want to override the output file action, uncomment line below
// $report->outputFileAction = Report::ACTION_GET_DOWNLOAD_URL;

// Configure your report data. Each of the keys must match the template 
// variables set in your MS Word template and each value will be the
// evaluated to replace the Word template variable. If the value is an 
// array, it will treated as tabular data.
$report->templateVariables = [
    'client_name' => 'Murat Cileli', 
    'address' => 'Kadikoy, Istanbul / Turkey', 
    'address' => 'Kadikoy, Istanbul / Turkey', 
    'date' => '10-Apr-2018', 
    'phone' => '+1-800-3399622', 
    'email' => 'admin@gmail.com', 
    'notes' => 'Thank you for your purchase.', 
    'quantities' => ['6', '3', '4'], 
    'products' => ['Apple iPhone 5S', 'Samsung Galaxy S5', 'Office 365 License'], 
    'prices' => ['490 USD', '399 USD', '199 USD'], 
];

// lastly generate the report
$report->generateReport();
```

### Step 7: Check your output report document

Your output should look something like below:

![Output Document Screenshot](https://www.php-reports.com/images/php-reports-values.png)

## License

**yii2-report** is released under the BSD 3-Clause License. See the bundled `LICENSE.md` for details.