# yii2-scholar-indicators
This extension facilitates the computation of [BIP! Scholar](https://bip.imis.athena-innovation.gr/scholar/profile) research-level impact indicators.

## Installation
The preferred way to install this extension is through composer.

Either run:
```
composer require --prefer-dist schatzopoulos/yii2-scholar-indicators "*"
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
