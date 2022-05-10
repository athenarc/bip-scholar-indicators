# BIP! Scholar Indicators
This extension facilitates the computation of [BIP! Scholar](https://bip.imis.athena-innovation.gr/scholar/profile) research-level impact indicators.

## Installation
The preferred way to install this extension is through composer.

Either run:
```
composer require "schatzopoulos/yii2-scholar-indicators"
```
or add
```
"schatzopoulos/yii2-scholar-indicators": "*"
```
to the required section of your `composer.json` file and execute `composer update`.

## Basic Usage
```
<?php

use schatzopoulos\scholarindicators\ScholarIndicators;

# mapping of impact fields
$impact_fields = [
  'popularity' => 'attrank',
  'influence' => 'pagerank',
  'impulse' => '3y_cc',
  'citations' => 'citation_count',
  'year' => 'year'
 ];

# different work types
$work_types = [
  'publication' => '',
  'dataset' => 1,
];

# provide impact data 
$impact_data = [
  0 => [
    'doi' => '10.1093/nar/gkx1141',
    'is_oa' => '1',
    'type' => NULL,
    'attrank' => '0.0000006354301544051926',
    'pagerank' => '0.000000028758916907015535',
    '3y_cc' => '229',
    'citation_count' => '406',
    'year' => '2017',
  ],
  1 => [
    'doi' => '10.1093/nar/gkw455',
    'is_oa' => '1',
    'type' => NULL,
    'attrank' => '0.000000029483090596793',
    'pagerank' => '0.0000000074224020960741795',
    '3y_cc' => '23',
    'citation_count' => '34',
    'year' => '2016',
  ]
];

# academic leaves 
$rag_data = [
  0 => [
    'id' => '159',
    'orcid' => '0000-0003-1714-5225',
    'start_date' => '2022-05-01',
    'end_date' => '2022-05-25',
    'description' => 'Parental Leave',
  ]
];

$indicators = new ScholarIndicators($impact_fields, $work_types, $impact_data);

$work_types_num = $indicators->work_types_num();
$citations_num = $indicators->citations_num();
$h_index = $indicators->h_index();
$i10_index = $indicators->i10_index();
$popularity = $indicators->popularity_sum();
$influence = $indicators->influence_sum();
$impulse = $indicators->impulse_sum();
$openness = $indicators->open_papers_percentage();
$paper_min_year = $indicators->get_paper_min_year();
$academic_age = $indicators->get_academic_age($paper_min_year);

$responsible_academic_age = ScholarIndicators::get_responsible_academic_age($academic_age, $rag_data, $paper_min_year);

?>
```

## Local Development
* git clone this repository in a directory
* add the following code to the `composer.json` of this extension:
```
    "minimum-stability": "dev",
```
* add the following code in the `composer.json` of your main project:
```
    "require-dev": {
        "schatzopoulos/yii2-scholar-indicators": "@dev"
    },
    "repositories": [
        {
            "type": "path",
            "url": "[absolute-path-to-cloned-directory]"
        }
    ]
 ```
 * run:
 ``
 composer update
``
inside the root folder of your main project.
* now your main projects should get live updates from the changes in the extension code. 
