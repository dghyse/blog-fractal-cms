# blog-fractal-cms

## prérequis

### Backend

* Php : >= 8.2

### Front

* Nodejs :v24.8.0
* Nmp :11.6.0

### build dist

### init node modules

```
npm install
```

#### In dev

```
npm run watch
```

#### For production

```
npm run dist-clean
```
## Init CMS

## Config application

### Add module fractal-cms in config file

```php 
    'bootstrap' => [
        'fractal-cms',
        //../..
    ],
    'modules' => [
        'fractal-cms' => [
            'class' => FractalCmsModule::class
        ],
        //../..
    ],
```


### Run migration

``
php yii.php migrate
``
### Create Rbac

``
php yii.php fractalCms:rbac/index
``

### Create Admin
``
php yii.php fractalCms:admin/create
``
### INIT content

``
php yii.php fractalCms:init/index
``
### Create Blog

``
php yii.php blog/build-cms-site
``



