<?php
/**
 * BlogController.php
 *
 * PHP Version 8.2+
 *
 * @author David Ghyse <david.ghyse@free.fr>
 * @version XXX
 * @package console\controllers
 */
namespace console\controllers;

use fractalCms\helpers\Cms;
use fractalCms\models\ConfigItem;
use fractalCms\models\ConfigType;
use fractalCms\models\Content;
use fractalCms\models\ContentItem;
use fractalCms\models\Item;
use fractalCms\models\Parameter;
use fractalCms\models\Seo;
use fractalCms\models\Slug;
use fractalCms\Module;
use yii\console\Controller;
use Exception;
use Yii;
use yii\helpers\Console;
use yii\helpers\Json;

class BlogController extends Controller
{

    protected $dataPath = '@data/blog';
    protected static $configJsonPath = '@data/blog/configuration.json';
    protected static $params = [
        'configJsonItems' => '@data/blog/itemConfigs.json',
        'configJsonTypes' => '@data/blog/typeConfigs.json',
    ];
    protected $configJsonItems = [];
    protected $configJsonTypes = [];
    protected $configurationJson = [];

    protected $configType;
    protected $configsItem = [];
    protected $parameter;

    public function init()
    {
        try {
            parent::init();
            foreach (static::$params as $attribut => $alias) {
                $path = Yii::getAlias($alias);
                Console::stdout('INIT JSON OK !!!! : '.$attribut."\n");
                if (file_exists($path) === true && $this->hasProperty($attribut) === true) {
                    $json = file_get_contents($path);
                    $this->{$attribut} = Json::decode($json);
                }
            }
            $configPath = Yii::getAlias(self::$configJsonPath);
            if (file_exists($configPath) === true) {
                $json = file_get_contents($configPath);
                $this->configurationJson = Json::decode($json);
            }

        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }

    }

    public function actionCreate()
    {
        try {
            foreach ($this->configJsonTypes as $name => $config) {
                $this->addConfigType($name, $config);
            }
            foreach ($this->configJsonItems as $name => $config) {
                $this->addItemConfigType($name, $config);
            }
            $newConfigurationJson = $this->addContents($this->configurationJson);
            $configPath = Yii::getAlias(self::$configJsonPath);
            file_put_contents($configPath, Json::encode($newConfigurationJson), JSON_PRETTY_PRINT);

        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    protected function addConfigType($name, $value) : bool
    {
        try {
            $configType = ConfigType::find()->andWhere(['name' => $name])->one();
            if ($configType === null) {
                $configType = Yii::createObject(ConfigType::class);
                $configType->name = $name;
                $configType->scenario = ConfigType::SCENARIO_CREATE;
            } else {
                $configType->scenario = ConfigType::SCENARIO_UPDATE;
            }
            $configType->config = $value;
            $success = false;
            if ($configType->validate() === true) {
                $success = $configType->save();
                $this->configType = $configType;
            } else {
                $this->configType = null;
            }
            return $success;
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    protected function addItemConfigType($name, $value) : bool
    {
        try {
            $configItem = ConfigItem::find()->andWhere(['name' => $name])->one();
            if ($configItem === null) {
                $configItem = Yii::createObject(ConfigItem::class);
                $configItem->name = $name;
                $configItem->scenario = ConfigItem::SCENARIO_CREATE;
            } else {
                $configItem->scenario = ConfigItem::SCENARIO_UPDATE;
            }
            $configItem->config = Json::encode($value);
            $success = false;
            if ($configItem->validate() === true) {
                $success = $configItem->save();
                $this->configsItem[$name] = $configItem;
            }else {
                $this->configsItem[$name] = null;
            }
            return $success;
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    protected function addParameter($main, $name, $value)
    {
        try {
            $parameter = Parameter::find()->andWhere(['group' => $main, 'name' => $name])->one();
            if ($parameter === null) {
                $parameter = Yii::createObject(Parameter::class);
                $parameter->scenario = Parameter::SCENARIO_CREATE;
                $parameter->group = $main;
                $parameter->name = $name;
            } else {
                $parameter->scenario = Parameter::SCENARIO_UPDATE;
            }
            $success = true;
            if ($parameter->validate() === true) {
                $success = $parameter->save();
                $this->parameter = $parameter;
            } else {
                $this->parameter = null;
            }
            return $success;
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    protected function addContents($configuration) : array
    {
        try {
            $newConfig = [];
            foreach ($configuration as $contentJson) {
                $content = Content::find()->andWhere(['pathKey' => $contentJson['pathKey']])->one();
                if ($content === null) {
                    $content = Yii::createObject(Content::class);
                    $content->scenario = Content::SCENARIO_CREATE;
                    $content->pathKey = $contentJson['pathKey'];
                    $content->active = 1;
                } else {
                    $content->scenario = Content::SCENARIO_UPDATE;
                }
                if(isset($contentJson['parentPathKey']) === true) {
                    $content->parentPathKey = $contentJson['parentPathKey'];
                }
                $content->name = $contentJson['name'];
                $content->type = $contentJson['model'];
                if ($this->configType !== null) {
                    $content->configTypeId = $this->configType->id;
                }
                /** @var Slug $slug */
                $slug = null;
                if (empty($content->slugId) === false) {
                    $slug = Slug::findOne($content->slugId);
                }
                if ($slug === null) {
                    $slug = Slug::find()->andWhere(['path' => $contentJson['slug']['path']])->one();
                }

                if ($slug === null) {
                    $slug = Yii::createObject(Slug::class);
                    $slug->scenario = Slug::SCENARIO_CREATE;
                } else {
                    $slug->scenario = Slug::SCENARIO_UPDATE;
                }

                $slug->path = $contentJson['slug']['path'];
                $slug->active = 1;
                if($slug->validate() === true) {
                    $success = $slug->save();
                    if ($success === true) {
                        $content->slugId = $slug->id;
                        Console::stdout('CREATE SLUG OK !!!! : '.$slug->id."\n");
                    }
                } else {
                    Console::stdout('CREATE SLUG KO !!!! : '.Json::encode($slug->errors, JSON_PRETTY_PRINT)."\n");
                }
                /** @var Seo $seo */
                $seo = null;
                if (empty($content->seoId) === false) {
                    $seo = Seo::findOne($content->seoId);
                    $seo->scenario = Seo::SCENARIO_UPDATE;
                }

                if ($seo === null) {
                    $seo = Yii::createObject(Seo::class);
                    $seo->scenario = Seo::SCENARIO_CREATE;
                }
                $seo->attributes = $contentJson['seo'];
                if ($seo->validate() === true) {
                    $dataFile = Module::getInstance()->filePath;
                    $relativeDirName = Module::getInstance()->relativeSeoImgDirName;
                    $seo->imgPath = $contentJson['seo']['imgPath'];
                    $seo->imgPath = $seo->saveFile($dataFile, $relativeDirName, $seo->imgPath, false);
                    $success = $seo->save();
                    if ($success === true) {
                        $content->seoId = $seo->id;
                        Console::stdout('CREATE SEO OK !!!! : '.$seo->id."\n");
                    }
                } else {
                    Console::stdout('CREATE SEO KO !!!! : '.Json::encode($seo->errors, JSON_PRETTY_PRINT)."\n");
                }
                $items = $contentJson['items'];
                if ($content->validate() === true) {
                    $content->save();
                    $newItems = [];
                    foreach ($items as $item) {
                        Console::stdout(' --- ADD ITEM ---  '."\n");
                        list($content, $newItem) = $this->addItems($content, $item);
                        $newItems[] = $newItem;
                    }
                    $content->manageItems(false);
                    $contentJson['items'] = $newItems;
                    Console::stdout('CREATE CONTENT OK !!!! : '.$content->id.' : '.$content->name."\n");
                } else {
                    Console::stdout('CREATE CONTENT KO !!!! : '.Json::encode($content->errors, JSON_PRETTY_PRINT)."\n");
                }
                $newConfig[] = $contentJson;
            }
            return $newConfig;
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    protected function addItems(Content $content, array $item) : array
    {
        try {
            $tempItem = $item;
            $name = $tempItem['name'];
            unset($tempItem['name']);
            if (isset($this->configsItem[$name]) === true && $this->configsItem[$name] instanceof ConfigItem) {
                $configItem = $this->configsItem[$name];
                $itemDbId = ($tempItem['id']) ?? null;
                $itemDb = Item::findOne($itemDbId);
                if ($itemDb === null) {
                    $itemDb = Yii::createObject(Item::class);
                    $itemDb->scenario = Item::SCENARIO_CREATE;
                    $itemDb->configItemId = $configItem->id;
                    if ($itemDb->validate() === true) {
                        $itemDb->save();
                        $content->attachItem($itemDb);
                        Console::stdout(' -- CREATE ITEM OK : '.$name."\n");
                    } else {
                        Console::stdout(' -- CREATE ITEM KO !!!! : '.Json::encode($itemDb->errors, JSON_PRETTY_PRINT)."\n");
                    }
                }
                $content->items[$itemDb->id] = $tempItem;
                $item['id'] = $itemDb->id;
            }
            return [
                $content,
                $item
            ];
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    protected function addMenu() : int
    {
        try {

        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    protected function addMenuItem() : int
    {
        try {

        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

}
