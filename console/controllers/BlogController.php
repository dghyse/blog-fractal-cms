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

use fractalCms\models\ConfigItem;
use fractalCms\models\ConfigType;
use fractalCms\models\Content;
use fractalCms\models\Item;
use fractalCms\models\Menu;
use fractalCms\models\MenuItem;
use fractalCms\models\Parameter;
use fractalCms\models\Seo;
use fractalCms\models\Slug;
use fractalCms\models\User;
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
    protected static $configJsonPathBuilded = '@data/blog/configuration_builded.json';
    protected static $params = [
        'configJsonItems' => '@data/blog/itemConfigs.json',
        'configJsonTypes' => '@data/blog/typeConfigs.json',
    ];
    protected $configJsonItems = [];
    protected $configJsonTypes = [];
    protected $configurationJson = [];

    protected $configType;
    protected $configsItem = [];
    protected $articleRoutes = [];

    /**
     * @inheritDoc
     */
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
            $configPath = Yii::getAlias(self::$configJsonPathBuilded);
            if (file_exists($configPath) === false) {
                $configPath = Yii::getAlias(self::$configJsonPath);
            }
            if (file_exists($configPath) === true) {
                $json = file_get_contents($configPath);
                $this->configurationJson = Json::decode($json);
            }

        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }

    }

    /**
     * Build Cms for Site blog
     *
     * @return void
     * @throws Exception
     */
    public function actionBuildCmsSite()
    {
        try {
            foreach ($this->configJsonTypes as $name => $config) {
                $this->addConfigType($name, $config);
            }
            foreach ($this->configJsonItems as $name => $config) {
                $this->addItemConfigType($name, $config);
            }
            //Add site name Parametre
            $sucess = $this->addParameter('SITE', 'NAME', 'Blog FractalCMS');

            $newConfigurationJson = $this->addContents($this->configurationJson);
            $configPath = Yii::getAlias(self::$configJsonPathBuilded);
            $this->configurationJson = $newConfigurationJson;
            file_put_contents($configPath, Json::encode($newConfigurationJson, JSON_PRETTY_PRINT));
            $this->addMenu($newConfigurationJson);
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    /**
     * Add Content config Type
     *
     * @param $name
     * @param $value
     * @return bool
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
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

    /**
     * Add Item Config Type
     *
     * @param $name
     * @param $value
     * @return bool
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
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
                $success = $this->addParameter('ITEM', $name, $configItem->id);
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

    /**
     * Add Parameter
     *
     * @param $main
     * @param $name
     * @param $value
     * @return bool
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    protected function addParameter($main, $name, $value)
    {
        try {
            $name = strtoupper(str_replace('-', '_', $name));
            $parameter = Parameter::find()->andWhere(['group' => $main, 'name' => $name])->one();
            if ($parameter === null) {
                $parameter = Yii::createObject(Parameter::class);
                $parameter->scenario = Parameter::SCENARIO_CREATE;
                $parameter->group = $main;
                $parameter->name = $name;
            } else {
                $parameter->scenario = Parameter::SCENARIO_UPDATE;
            }
            $parameter->value = (string)$value;
            $success = true;
            if ($parameter->validate() === true) {
                $success = $parameter->save();
                Console::stdout(' --- CREATE PARAMETRE OK : '.$main.' - '.$name."\n");
            } else {
                Console::stdout(' --- CREATE PARAMETRE KO : '.Json::encode($parameter->errors, JSON_PRETTY_PRINT)."\n");
            }
            return $success;
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    /**
     * Create or Update Content
     *
     * @param $configuration
     * @return array
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
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
                    $indexItem = 0;
                    foreach ($items as $item) {
                        Console::stdout(' --- ADD ITEM ---  '."\n");
                        list($content, $newItem) = $this->addItems($content, $item, $indexItem);
                        $newItems[] = $newItem;
                    }
                    $content->manageItems(false);
                    $contentJson['items'] = $newItems;
                    $contentJson['contentId'] = $content->id;
                    $this->articleRoutes[] = $content->getRoute();
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

    /**
     * Add Items og Content
     *
     *
     * @param Content $content
     * @param array $item
     * @param $index
     * @return array
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    protected function addItems(Content $content, array $item, &$index) : array
    {
        try {
            $tempItem = $item;
            // Remove name
            $name = $tempItem['name'];
            unset($tempItem['name']);
            if (isset($this->configsItem[$name]) === true && $this->configsItem[$name] instanceof ConfigItem) {
                $configItem = $this->configsItem[$name];
                $itemDbId = ($tempItem['id']) ?? null;
                //Remove id
                unset($tempItem['id']);
                if ($name === 'image-html'
                    && isset($this->articleRoutes[$index]) === true
                    && empty($item['target']) === false) {
                    $tempItem['target'] = $this->articleRoutes[$index];
                    $item['target'] = $this->articleRoutes[$index];
                    $index += 1;
                }
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

    /**
     * Add Menu
     *
     * @param $configuration
     * @return bool
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    protected function addMenu($configuration) : bool
    {
        try {
            $success = true;
            $menuNames = [
                'header',
                'footer'
            ];
            foreach ($menuNames as $menuName) {
                $menu = Menu::find()->andWhere(['name' => $menuName])->one();
                if ($menu === null) {
                    $menu = Yii::createObject(Menu::class);
                    $menu->scenario = Menu::SCENARIO_CREATE;
                    $menu->name = $menuName;
                    $menu->active = 1;
                }
                if ($menu->validate() === true) {
                    $menu->save();
                    $menu->refresh();
                    Console::stdout(' ---- CREATE MENU OK : '.$menuName."\n");
                    $configurationMenu = array_reverse($configuration);
                    foreach ($configurationMenu as $index => $content) {
                        $this->addMenuItem($menu, $content['contentId'], $content['name'], $index);
                    }
                    $this->addParameter('MENU', strtoupper($menuName), $menu->id);
                } else {
                    $success = false;
                    Console::stdout(' ---- CREATE MENU KO : '.Json::encode($menu->errors, JSON_PRETTY_PRINT)."\n");
                }
            }
            return $success;
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    /**
     * Add Menu Item
     *
     * @param Menu $menu
     * @param $contentId
     * @param $name
     * @param $index
     * @return bool
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    protected function addMenuItem(Menu $menu, $contentId, $name, $index) : bool
    {
        try {
            $success = true;
            $menuItem = $menu->getMenuItems()->andWhere(['name' => ucfirst($name)])->one();
            if ($menuItem === null) {
                $menuItem = Yii::createObject(MenuItem::class);
                $menuItem->scenario = MenuItem::SCENARIO_CREATE;
                $menuItem->menuId = $menu->id;
            } else {
                $menuItem->scenario = MenuItem::SCENARIO_UPDATE;
            }
            $menuItem->contentId = $contentId;
            $menuItem->name = ucfirst($name);
            $menuItem->order = $index + 1;
            if ($menuItem->validate() === true) {
                $menuItem->save();
                Console::stdout(' ---- CREATE MENU ITEM OK : '.$name.' order KEY : '.($index + 1)."\n");
            } else {
                $success = false;
                Console::stdout(' ---- CREATE MENU ITEM KO : '.Json::encode($menuItem->errors, JSON_PRETTY_PRINT)."\n");
            }
            return  $success;
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

}
