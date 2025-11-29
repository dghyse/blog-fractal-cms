<?php

namespace webapp\widgets;

use fractalCms\content\models\Content;
use fractalCms\content\models\Item as ItemModel;
use fractalCms\content\models\Tag;
use fractalCms\core\models\Parameter;
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
                if ($this->item->configItemId == Parameter::getParameter('ITEM', 'CARD_ARTICLE')) {
                    $target = $this->item?->target;
                    $targetRoute = $target;
                    if (is_string($target) === true) {
                        $urls = explode('/', trim($target, '/'));
                        if(count($urls) === 2 && is_string($urls[1])) {
                            $params = explode('-', $urls[1]);
                            if (count($params) === 2 && is_string($params[1])) {
                                $id = $params[1];
                                if ($params[0] === 'tag') {
                                    $targetContent = Tag::findOne($id);
                                } else {
                                    $targetContent = Content::findOne($id);
                                }
                            }
                        }
                    }
                    if ($targetContent instanceof Content) {
                        $targetRoute = $targetContent->getRoute();
                        $itemEntete = $targetContent->getItemByConfigId(Parameter::getParameter('ITEM', 'ENTETE'));
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
