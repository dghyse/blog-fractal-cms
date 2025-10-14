<?php

namespace webapp\widgets;

use fractalCms\helpers\Cms;
use fractalCms\models\Content;
use fractalCms\models\Item as ItemModel;
use yii\base\Widget;
use Yii;
use Exception;


class Item extends Widget
{

    public Content $element;
    public ItemModel $item;


    /**
     * {@inheritDoc}
     */
    public function run()
    {
        try {
            Yii::debug('Trace: '.__METHOD__, __METHOD__);
            $content = null;
            if ($this->item instanceof ItemModel) {
                $targetRoute = null;
                $targetContent = null;
                $itemEntete = null;
                $skills = [];
                if ($this->item->configItemId == Cms::getParameter('ITEM', 'CARD_ARTICLE')) {
                    $target = $this->item?->target;
                    $targetRoute = $target;
                    $id = $target;
                    if (is_string($target) === true) {
                        $params = explode('-', $target);
                        if(count($params) === 2 && is_numeric($params[1])) {
                            $id = $params[1];
                        }
                    }
                    $targetContent = Content::findOne($id);
                    if ($targetContent instanceof Content) {
                        $targetRoute = $targetContent->getRoute();
                        $itemEntete = $targetContent->getItemByConfigId(Cms::getParameter('ITEM', 'ENTETE'));
                    }
                }

                $view = 'item-'.strtolower($this->item->configItem->name);
                $content =  $this->render($view, [
                    'item' => $this->item,
                    'targetRoute' => $targetRoute,
                    'targetContent' => $targetContent,
                    'itemEntete' => $itemEntete,
                ]);
            }
            return $content;
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
        }
    }
}
