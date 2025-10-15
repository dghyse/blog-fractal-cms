<?php

namespace webapp\widgets;

use webapp\helpers\MenuBuilder;
use yii\base\Widget;
use Yii;
use Exception;

/**
 * Class MenuNav
 */
class MenuNav extends Widget
{

    public $element;
    public $name;
    protected $menuBuilder;


    /**
     * @inheritDoc
     */
    public function __construct(MenuBuilder $menuBuilder, $config = [])
    {
        try {
            parent::__construct($config);
            $this->menuBuilder = $menuBuilder;
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        try {
            Yii::debug('Trace: '.__METHOD__, __METHOD__);
            $menu = [];
            if ($this->menuBuilder instanceof MenuBuilder) {
                $name = (isset($this->name) === true) ? $this->name : 'HEADER';
                $menu = $this->menuBuilder->get($name);
            }
            return $this->render('menu-nav', [
                'menu' => $menu
            ]);
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
        }
    }
}
