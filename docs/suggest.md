## 最佳实践

### phplint

[phplint](https://github.com/overtrue/phplint)是一个快速检测php语法错误的工具, 此工具无需安装在项目中, 全局安装即可. 

```
$ composer global require overtrue/phplint -vvv
$ php artisan system:doc lint
$ phplint /path/of/code -c /framework/path/.phplint.yml
```

### optimize

运行 `php artisan poppy:optimize` 保障依赖组件均已经安装