# BIP! Scholar Indicators
This extension facilitates the computation of [BIP! Scholar](https://bip.imis.athena-innovation.gr/scholar) research-level impact indicators.


## Example usage

This [example](./example.php) demonstrates how to use the `ScholarIndicators` class to compute various researcher-level scholarly indicators based on sample data. 
It implements the following indicators:

- **Work Types Count**: Counts the number of works (papers, datasets, software, etc.).
- **Total Citations**: Sums up the total citation count across all works.
- **h-Index & i10-Index**: Computes the h-index and i10-index based on citation counts.
- **Popularity, Influence, and Impulse Scores**: Sums the popularity, influence, and impulse scores for all works.
- **Popular & Influential Works Count**: Counts popular and infuential works (i.e., those that have impact class other than the lowest one).
- **Open Access Papers Percentage**: Calculates the percentage of open access papers.
- **Academic Age**: Computes the academic age based on the earliest paper.
- **Responsible Academic Age**: Considers periods of inactivity to adjust academic age.

To run the example script, simply download or clone the repository and execute the `example.php` file with PHP:

```bash
php example.php
```

## Installation in Yii2 projects
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
