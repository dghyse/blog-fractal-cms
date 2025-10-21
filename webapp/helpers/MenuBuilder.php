<?php
/**
 * MenuBuilder.php
 *
 * PHP Version 8.2+
 *
 * @author David Ghyse <david.ghysefree.fr>
 * @version XXX
 * @package app\config
 */
namespace webapp\helpers;

use fractalCms\helpers\Cms;
use fractalCms\models\Content;
use fractalCms\models\Menu;
use fractalCms\models\MenuItem;
use yii\base\Component;
use Yii;
use Exception;
use yii\db\ActiveQuery;

class MenuBuilder extends Component
{

    public $menu = [];

    public function get($name)
    {
        try {
            if (empty($this->menu[$name]) === true) {
                $result = [];
                $menuId = Cms::getParameter('MENU', $name);
                $menu = Menu::findOne($menuId);
                if ($menu instanceof Menu) {
                    $menuItemsQuery = $menu->getMenuItems(true)->orderBy(['order' => SORT_ASC]);
                    $result = $this->build($menuItemsQuery);
                }
                $this->menu[$name] = $result;
            }
            return $this->menu[$name];
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }

    }



    protected function build(ActiveQuery $menuItemsQuery)
    {
        try {
            $result  = [];
            /** @var MenuItem $menuItem */
            foreach ($menuItemsQuery->each() as $menuItem) {
                $part = [];
                $contentTarget = $menuItem->getContent()->andWhere(['active' => 1])->one();
                if ($contentTarget instanceof Content || empty($menuItem->route) === false) {
                    $route = ($contentTarget !== null) ? $contentTarget->getRoute() : $menuItem->route;
                    $part['name'] = $menuItem->name;
                    $part['route'] = $route;
                    $subMenuQuery = $menuItem->getMenuItems();
                    if ($subMenuQuery->count() > 0 ) {
                        $part['child'] = $this->build($menuItem->getMenuItems());
                    }
                    $result[] = $part;
                }
            }
            return $result;
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }

    }
}
