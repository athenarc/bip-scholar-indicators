# yii2-scholar-indicators
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
use schatzopoulos\scholarindicators\ScholarIndicators;

$indicators = new ScholarIndicators(
            Yii::$app->params['impact_fields'], 
            array_flip(Yii::$app->params['work_types']), 
            $impact_data
);

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
