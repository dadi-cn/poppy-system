<?php
// 单点登录
Route::group([
    'middleware' => ['api-sso'],
    'namespace'  => 'Poppy\System\Http\Request\ApiV1\Backend',
], function (Illuminate\Routing\Router $route) {
    $route->post('menu/lists', 'MenuController@lists')
        ->name('py-system:backend.menu.lists');
});
