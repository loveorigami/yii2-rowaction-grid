<?php

/**
 * @copyright Copyright (c) Roman Korolov, 2015
 * @link https://github.com/rokorolov
 * @license http://opensource.org/licenses/BSD-3-Clause
 * @version 1.0.0
 */

namespace rokorolov\rowaction;

use rokorolov\rowaction\RowActionAsset;
use rokorolov\helpers\Html;
use Yii;
use Closure;
use yii\grid\DataColumn;
use yii\helpers\Url;

/**
 * RowAction is a column for the GridView widget that displays links for viewing and manipulating the items.
 *
 * @author Roman Korolov <rokorolov@gmail.com>
 */
class RowAction extends DataColumn
{
    use \rokorolov\base\TranslationTrait;
    
    /**
     * @var string|callable
     */
    public $prepend = '';
    
    /**
     * @var string|callable 
     */
    public $append = '';
    
    /**
     * @var string 
     */
    public $controller;
    
    /**
     * @var string 
     */
    public $template = '{view} {update} {delete} ';
    
    /**
     * @var callable 
     */
    public $urlCreator;
    
    /**
     * @var array 
     */
    public $actionOptions = [];
    
    /**
     * @var array 
     */
    public $buttons = [];
    
    /**
     * @var string 
     */
    public $deleteMessage;
    
    /**
     * @var array 
     */
    public $i18n = [];
    
    /**
     * @var string 
     */
    protected $_messageCategory;
    

    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();

        $this->_messageCategory = 'rk-rowaction';
        $this->initI18N('@rokorolov/rowaction', $this->_messageCategory);
        
        if ($this->deleteMessage === null) {
            $this->deleteMessage = Yii::t($this->_messageCategory, 'Are you sure you want to delete this item?');
        }
        Html::addCssClass($this->actionOptions, 'rk-row-actions');
        $this->initDefaultButtons();
        
        $this->registerScripts();
    }
    
    /**
     * Initializes the default button rendering callbacks.
     */
    public function initDefaultButtons()
    {
        if (!isset($this->buttons['view'])) {
            $this->buttons['view'] = function ($url, $model, $key) {
                return Html::a(Html::icon('eye', ['class' => 'text-warning']) . ' ' . Yii::t($this->_messageCategory, 'View'), $url, [
                    'data-pjax' => '0',
                ]);
            };
        }
        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function ($url, $model, $key) {
                return Html::a(Html::icon('pencil', ['class' => 'text-success']) . ' ' . Yii::t($this->_messageCategory, 'Edit'), $url, [
                    'data-pjax' => '0',
                ]);
            };
        }
        if (!isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function ($url, $model, $key) {
                return Html::a(Html::icon('times', ['class' => 'text-danger']) . ' ' . Yii::t($this->_messageCategory, 'Delete'), $url, [
                    'data-confirm' => $this->deleteMessage,
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ]);
            };
        }
    }
    
    /**
     * Creates a prepend content.
     * 
     * @param \yii\db\ActiveRecord $model the data model
     * @param mixed $key the key associated with the data model
     * @param integer $index the current row index
     * @return string the created prepend content
     */
    public function getPrependContent($model, $key, $index)
    {
        if($this->prepend === '') {
            return Html::a(Html::encode($model->{$this->attribute}), [$this->controller ? $this->controller . '/' . 'update' : 'update', 'id' => $key], ['data-pjax' => 0, 'class' => 'rk-row-title']);
        } else {
            return ($this->prepend instanceof Closure) ? call_user_func($this->prepend, $model, $key, $index) : $this->prepend;
        }
    }
    
    /**
     * Creates a URL for the given action and model.
     * This method is called for each button and each row.
     *
     * @param string $action the button name (or action ID)
     * @param \yii\db\ActiveRecord $model the data model
     * @param mixed $key the key associated with the data model
     * @param integer $index the current row index
     * @return string the created URL
     */
    public function createUrl($action, $model, $key, $index)
    {
        if ($this->urlCreator instanceof Closure) {
            return call_user_func($this->urlCreator, $action, $model, $key, $index);
        } else {
            $params = is_array($key) ? $key : ['id' => (string)$key];
            $params[0] = $this->controller ? $this->controller . '/' . $action : $action;
            
            return Url::toRoute($params);
        }
    }
    
    /**
     * Creates a data cell content
     * 
     * @param \yii\db\ActiveRecord $model the data model
     * @param mixed $key the key associated with the data model
     * @param integer $index the current row index
     * @return string the created cell content
     */
    public function renderDataCellContent($model, $key, $index)
    {
        preg_match_all('/\\{([\w\-\/]+)\\}/', $this->template, $matches);
        $items[] = $this->getPrependContent($model, $key, $index);
        $items[] = Html::beginTag('div', $this->actionOptions);
        foreach ($matches[1] as $name) {
            if (isset($this->buttons[$name])) {
                $url = $this->createUrl($name, $model, $key, $index);
                $item = call_user_func($this->buttons[$name], $url, $model, $key, $index);
                if ($item !== '') {
                    $items[] = Html::tag('span', $item, ['class' => $name]);
                }
            }
        }
        $items[] = Html::endTag('div');
        $items[] = ($this->append instanceof Closure) ? call_user_func($this->append, $model, $key, $index) : $this->append;
        
        return implode("\n", $items);
    }
    
    /**
     * Register Client Scripts
     */
    public function registerScripts()
    {
        $view = $this->grid->getView();
        
        RowActionAsset::register($view);
    }
}
