<?php

namespace webapp\behaviors;

use fractalCms\controllers\CmsController;
use fractalCms\helpers\Cms;
use fractalCms\helpers\Html;
use fractalCms\models\Content;
use fractalCms\models\Item;
use fractalCms\models\Seo;
use webapp\assets\StaticAsset;
use webapp\widgets\Breadcrumb;
use yii\base\Behavior;
use Exception;
use Yii;
use yii\base\Widget;
use yii\helpers\Url;
use fractalCms\components\View;

class JsonLd extends Behavior
{

    public function events()
    {
        return [
            CmsController::EVENT_CONTENT_READY => 'contentReady',
            Widget::EVENT_AFTER_RUN => 'jsonBreadcrumbWidget',
        ];
    }

    public function contentReady($event)
    {
        try {
            $controller = $this->owner;
            if ($controller instanceof CmsController) {
                $target = $controller->getTarget();
                if ($target !== null) {
                    $seo = $target->getSeo()->one();
                    $activeJsonLd = false;
                    if(($seo instanceof Seo) && (boolean)$seo->addJsonLd === true) {
                        $activeJsonLd = true;
                    }
                    if ($activeJsonLd === true) {
                        $view = $controller->getView();
                        $this->buildWebSite($view);
                    }
                }
            }
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw  $e;
        }
    }

    public function jsonBreadcrumbWidget($event)
    {
        try {

            $widget = $this->owner;
            /** @var View $view */
            $view = Yii::$app->controller->getView();
            if ($widget instanceof Breadcrumb) {
                $data = $widget->buildBreadcrumb();
                $activeJsonLd = false;
                $content = $widget->content;
                if ($content !== null) {
                    $seo = $content->getSeo()->one();
                    if(($seo instanceof Seo) && (boolean)$seo->addJsonLd === true) {
                        $activeJsonLd = true;
                    }
                }
                if ($activeJsonLd === true) {
                    $this->buildBreadcrumb($data, $view);
                }

            }
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw  $e;
        }
    }

    public function buildBreadcrumb(array $data, View $view)
    {
        try {
            $json = [];
            if (empty($data) == false) {
                $json['@context'] = 'https://schema.org';
                $json['@type'] = 'BreadcrumbList';
            }
            $itemListElement = [];
            foreach ($data as $index => $datum) {
                $item = [];
                $item['@type'] = 'ListItem';
                $item['position'] = ($index + 1);
                $item['name'] = $datum['name'];
                if (isset($datum['route']) === true) {
                    $item['item'] =  Url::toRoute($datum['route'], true);
                }
                $itemListElement[] = $item;
            }
            if ((empty($json) === false) && (empty($itemListElement) === false)) {
                $json['itemListElement'] = $itemListElement;
                $view->registerJsonLd($json);
            }
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw  $e;
        }
    }

    public function buildWebSite(View $view)
    {
        try {
            $staticUrl = StaticAsset::register($view)->baseUrl;
            $json = [];
            $json['@context'] = 'https://schema.org';
            $json['@type'] = 'WebSite';
            $json['name'] = 'Mon blog';
            $json['url'] = Yii::$app->urlManager->getHostInfo();
            $json['publisher'] = [
                '@type' => 'Person',
                'name' => 'firstname lastname',
                'image' => Yii::$app->urlManager->getHostInfo().$staticUrl.'/img/logo.webp',
            ];
            $view->registerJsonLd($json);
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw  $e;
        }
    }
}
