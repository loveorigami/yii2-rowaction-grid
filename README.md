yii2-rowaction-grid
===================
RowAction is a column for the GridView widget that displays links for viewing and manipulating the items.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist rokorolov/yii2-rowaction-grid "*"
```

or add

```
"rokorolov/yii2-rowaction-grid": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
use rokorolov\rowaction\RowAction;

'columns' => [
    // ...
    [
        'class' => RowAction::className(),
        'attribute' => 'title',
        // you may configure additional properties here
    ],
]
```