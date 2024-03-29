<?php
// 全局中间件定义文件
return [
    // 全局请求缓存
    // \think\middleware\CheckRequestCache::class,
    // 多语言加载
    // \think\middleware\LoadLangPack::class,
    // Session初始化
     \think\middleware\SessionInit::class,

    // 允许跨域请求 (如果没有跨域需求可将此处代码屏蔽以提升安全性)
    \cores\middleware\AllowCrossDomain::class,

    // 应用日志记录
    \cores\middleware\AppLog::class,
];
