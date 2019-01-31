<?php

namespace LumenTool\Wop;

use Closure;


//$app->routeMiddleware([
//    'WopWxAuth' => LumenTool\Wop\WopWxAuth::class,
//]);

//$router->group(['middleware' => 'WopWxAuth'], function ($router) {
//
//});
class WopWxAuth
{
    public function handle($request, Closure $next)
    {
        $open_id = WopUserInfo::getWxOpenId();
        if (empty($open_id)) {
            return _output("请用微信打开！", false);
        } else {
            return $next($request);
        }
    }
}
