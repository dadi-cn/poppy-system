# IDE 配置

## 项目配置

### 可以隐藏的目录
> 右键忽略掉即可, 这个是生成的文件, 不需要进行 php 索引
```
# 前端文件
public/assets/css
public/assets/font/fontawesome
public/assets/js/system_cp.js
public/assets/js/system_vendor.js
public/assets/easy-web

# 系统缓存, 文档
storage/phplint
storage/sami
public/docs/*
```

## 插件配置

### 插件 [.ignore](https://plugins.jetbrains.com/plugin/7495--ignore)

可以在编辑器忽略文件显示的组件

[.ignore 示例文件](https://gist.github.com/imvkmark/15198641b214b35916cf54414516caf0)

### 插件 [Laravel Plugin](https://plugins.jetbrains.com/plugin/7532-laravel-plugin)

**启用 插件**

找到 `Preferences | Languages & Frameworks | PHP | Laravel`, 然后开启 `Enable Plugin for this project`

**配置 view 的映射**
例如 `system` 模块的映射地址应该是 `modules/system/resources/views`

这样在点击的时候才能够跳转到这个页面

**启用控制器的命名空间检测**

在 `Router Namespace` 中添加相关的命名空间, 多个使用 `,` 分隔.

### 插件 [php inspection](https://plugins.jetbrains.com/plugin/7622-php-inspections-ea-extended-)

开启之后需要需要在写 PHP 的时候注意项目, [相关的文档点击](https://github.com/kalessil/phpinspectionsea/tree/master/docs)

### 插件 [String Manipulation](https://plugins.jetbrains.com/plugin/2162-string-manipulation)

> 提供字符的便捷操作

### 插件 [CamelCase](https://plugins.jetbrains.com/plugin/7160-camelcase)

> 提供大小写转换

