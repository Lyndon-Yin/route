## Laravel路由简介

基于Laravel的通用路由解析

## 使用简介

首先routes/api.php或者routes/web.php添加以下代码：
```php
use Illuminate\Http\Request;

Route::group([
    'prefix' => "admin",
    'middleware' => [
        // 中间件列表
    ]
], function ($router) {
    $router->any('{slug?}', function (Request $request) {
        return \Lyndon\Route\Action\Path4Router::route($request);
    })->where('slug', '(.*)?');
});
```

然后在app/Http/Controllers中添加如下目录结构：
```
AppType1
├── Module1
│   ├── Controller2
│   │   ├── Action1.php
│   │   ├── Action2.php
│   │   └── Action3.php
│   └── Controller2
└── Module2
```

AppType：接口类型，例如Admin（商家端），Client（用户端）等  
Module：模块，例如Goods（商品模块），Marketing（营销模块）等  
Controller：控制器，例如Brand（品牌控制），Stock（库存控制）等  
Action：方法，例如BrandList（品牌列表方法），BrandCreate（品牌添加方法）等  

```php
namespace App\Http\Controllers\Admin\ShopGoods\Brand;


use Illuminate\Http\Request;
use Lyndon\Route\Action\AbstractAction;

class BrandList extends AbstractAction
{
    public function allowMethod()
    {
        return self::METHOD_GET;
    }

    public function onRun(Request $request)
    {
        return 'brandList';
    }
}
```

AppType，Module，Controller均为目录，Action为Class类，其中onRun()方法是具体执行方法

## config配置
app/config目录下添加LyndonRoute.php配置文件：
```php
return [
    /*
     * 路由解析根目录，默认是App\Http\Controllers
     * 在这目录下可以创建appType，Module，Controller等目录
     */
    'actionDir' => 'App\\Http\\Controllers',
];
```
