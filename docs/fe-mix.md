# Mix 在项目中的使用

## webpack.mix.js 的配置

```
mix
    .browserSync({
        // 这里替换地址
        proxy : 'http://dev.{domain}.com/',
        files : [
            "public/resources/js/**/*.js",
            "public/resources/css/*.css",
            "modules/**/resources/views/**/*.blade.php",
            "modules/**/resources/mixes/**/*.js",
            "modules/**/resources/js/**/*.js"
        ]
    })
    .disableNotifications()
    .version()
    /*
    |--------------------------------------------------------------------------
    | develop & backend
    |--------------------------------------------------------------------------
    */
    /* system js
     * ---------------------------------------- */
    .combine([
            'modules/system/resources/libs/jquery/2.2.4/jquery.min.js',
            'modules/system/resources/libs/jquery/form/jquery.form.js',
            'modules/system/resources/libs/jquery/toastr/jquery.toastr.js',
            'modules/system/resources/libs/jquery/pjax/jquery.pjax.js',
            'modules/system/resources/libs/jquery/poshytip/jquery.poshytip.js',
            'modules/system/resources/libs/jquery/validation/jquery.validation.js',
            'modules/system/resources/libs/jquery/data-tables/jquery.data-tables.js',
            'modules/system/resources/libs/jquery/tokenize2/jquery.tokenize2.js',
            'modules/system/resources/libs/jquery/spinner/jquery.spinner.js',
            'modules/system/resources/libs/waves/waves.min.js',
            'modules/system/resources/libs/vue/vue.min.js',
            'modules/system/resources/libs/underscore/underscore.js',
            // hash, 加密使用 @ develop
            'modules/system/resources/libs/jshash/md5.min.js',
            'modules/system/resources/libs/jshash/sha1.min.js',
            // 粘贴板
            'modules/system/resources/libs/clipboard/clipboard.min.js',
            // 编辑器
            'modules/system/resources/libs/simditor/module.js',
            'modules/system/resources/libs/simditor/hotkeys.js',
            'modules/system/resources/libs/simditor/uploader.js',
            'modules/system/resources/libs/simditor/simditor.js',
            // 鼠标滑过提示
			'modules/system/resources/libs/popper.js/popper.min.js',
			'modules/system/resources/libs/bootstrap/js/util.js',
			'modules/system/resources/libs/bootstrap/js/tooltip.js',
            // 图片轮询显示
            'modules/system/resources/libs/jquery/fancybox/jquery.fancybox.min.js',
    	],
    	'public/assets/js/system_vendor.js'
    )
    .combine([
            // editor
            'modules/system/resources/libs/ace/ace.js',
            'modules/system/resources/libs/jquery/backstretch/jquery.backstretch.min.js',
            'modules/system/resources/libs/poppy/util.js',
            'modules/system/resources/libs/poppy/cp.js',
            'modules/system/resources/libs/poppy/system/cp.js'
    	],
    	'public/assets/js/system_cp.js'
    )
    /* system css
     * ---------------------------------------- */
    .sass(
    	'modules/system/resources/scss/system.scss',
    	'public/assets/css/system.css'
    )
    .copyDirectory('modules/system/resources/libs/layui', 'public/assets/layui')
    .copyDirectory('modules/system/resources/libs/easy-web', 'public/assets/easy-web')
    .copyDirectory('modules/system/resources/images/libs', 'public/assets/images/libs')
    .copyDirectory('modules/system/resources/images/system', 'public/assets/images/default')
    .copyDirectory('modules/system/resources/fonts/fontawesome', 'public/assets/font/fontawesome')
```