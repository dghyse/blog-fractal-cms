<?php

namespace webapp\widgets;

use fractalCms\core\models\Parameter;
use webapp\helpers\MenuBuilder;
use yii\base\Widget;
use Yii;
use Exception;
class Footer extends Widget
{

    public $element;
    protected $menuBuilder;

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
                $menu = $this->menuBuilder->get('FOOTER');
            }
            $siteName = Parameter::getParameter('SITE', 'NAME');
            return $this->render('footer', [
                'element' => $this->element,
                'menu' => $menu,
                'siteName' => $siteName
            ]);
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
        }
    }
}
