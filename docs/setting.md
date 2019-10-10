# 项目设置

## composer 配置

### 开发文件不需要自动加载
- 项目中使用 IDE Helper 生成浏览器提示文件, 此文件在正式项目下不需要进行加载
- Clockwork 不需要加载

```
"extra" : {
    "laravel" : {
        "dont-discover" : [
            "itsgoingd/clockwork",
            "barryvdh/laravel-ide-helper",
        ]
    }
},
```

### 映射 Form , 需要在 composer 中加入数据

由于这里是继承的 "laravelcollective/html" 组件, 所以必须先禁用掉原生的自动发现

在 composer.json 文件中禁用自动发现
```
"extra" : {
    "laravel" : {
        "dont-discover" : [
            "laravelcollective/html"
        ]
    }
},
```

在 `providers` 部分加入

```php
'providers' => [
    // ...
    Collective\Html\HtmlServiceProvider::class,    
    // ...
];
```

生成自动加载类
```
composer dumpautoload
```

清空缓存的数据
```
php artisan poppy:optimize
```

然后在 `app.php` 的 `aliases` 部分加入

```php
'aliases' => [
    // ...
    'Html' => Collective\Html\HtmlFacade::class,
    'Form' => System\Classes\Facade\FormFacade::class,
    // ...
];
```


## 模块配置 (`config/module.php`)
### 隐藏功能
> 这里的功能的隐藏是从隐藏入口进行处理的, 配置位置在 `config/module.php`
```
// 系统模块配置
'system' => [
    // 隐藏的路由功能
    'route_hide' => [
        'system:backend.addon.index',
        'system:backend.mail.store',
    ],
],
```

### 配置命名空间支持 单元测试

因为单元测试需要识别路径, 这里需要配置 psr-4 的映射
需要配置1个地址即可, 否则使用 phpunit 进行单元测试的时候无法进行有效的类加载.

```
"autoload-dev" : {
    "classmap" : [
    ],
    "psr-4" : {
        "System\\Tests\\" : "modules/system/tests/"
    }
},
```