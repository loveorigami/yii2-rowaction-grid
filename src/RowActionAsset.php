<?php

/**
 * @copyright Copyright (c) Roman Korolov, 2015
 * @link https://github.com/rokorolov
 * @license http://opensource.org/licenses/BSD-3-Clause
 * @version 1.0.0
 */

namespace rokorolov\rowaction;

/**
 * RowActionAsset is a bundle for the RowAction widget.
 *
 * @author Roman Korolov <rokorolov@gmail.com>
 */
class RowActionAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@rokorolov/rowaction/assets';
    public $css = [
        'css/rowaction.css'
    ];
}
