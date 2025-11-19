<?php
/**
 * manu-nav.php
 *
 * PHP Version 8.2+
 *
 * @version XXX
 * @package webapp\views\layouts
 *
 * @var $this yii\web\View
 * @var array $menu
 *
 */

use fractalCms\content\helpers\Html;

use yii\helpers\Url;
use webapp\assets\StaticAsset;
$baseUrl = StaticAsset::register($this)->baseUrl;
?>
<nav id="main-nav" role="navigation"  aria-label="Menu principal" blog-front-menu="">
    <button id="menu-toggle"
            class="md:hidden  focus:outline-none"
            aria-controls="main-menu-nav"
            aria-expanded="false"
            aria-label="Ouvrir le menu">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
             viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>
    <ul id="main-menu-nav" class="hidden md:flex flex-col md:flex-row gap-2 md:gap-6 bg-[var(--card)]  absolute md:static z-40 left-0 w-full border-t md:border-0 shadow-md md:shadow-none">
        <?php foreach ($menu as $index => $itemMenu) :
            $child = ($itemMenu['child']) ?? null;
            $idNav = 'nav-'.($index + 1);
            $classes = [];
            if ($child !== null) {
                $classes[] = 'relative';
            }
            echo Html::beginTag('li', ['class' => implode(' ', $classes)]);
            if ($child == null) {
                echo Html::a($itemMenu['name'], Url::toRoute($itemMenu['route']), ['class' => 'block px-3 py-2 hover:text-blue-600 transition whitespace-nowrap']);
            } else {
                echo Html::beginTag('button', [
                    'type' => 'button',
                    'aria-controls' => $idNav,
                    'aria-expanded' => 'false',
                    'class' => 'inline-flex items-center px-3 py-2 hover:text-blue-600 transition whitespace-nowrap']);
                echo $itemMenu['name'];
                ?>
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-4 h-4 ml-2 shrink-0"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
                <?php
                echo Html::endTag('button');
                echo Html::beginTag('ul', [
                    'class' => 'submenu-container absolute top-full left-0 mt-2 w-48  border border-gray-200 rounded-md shadow-lg hidden',
                    'aria-expanded' => 'false',
                    'aria-hidden' => 'true',
                    'id' => $idNav,
                ]);
                foreach ($child as $subChild) {
                    echo Html::tag('li',
                        Html::a(
                            $subChild['name'],
                            Url::toRoute($subChild['route']),
                            ['class' => 'block px-4 py-2 hover:bg-gray-50 hover:text-blue-600']
                        )
                    );
                }
                echo Html::endTag('ul');
            }

            echo Html::endTag('li');
        endforeach;
        ?>
    </ul>
</nav>
