<?php
return array(
        //'配置项'=>'配置值'
        'APP_DEBUG' =>  false , // 开启调试模式 

        'URL_CASE_INSENSITIVE' =>   true, //不区分大小写

        'DB_TYPE'=> 'Pdo',    // 数据库类型 

        'DB_PREFIX'=>'', // 数据表前缀 

        'URL_DISPATCH_ON'=> true,

        'URL_MODEL' =>2,//启用REWRITE模式 

        'APP_GROUP_LIST'=>'Home,Admin,Setting,Archives,Card,Monitor,Report,Log',//分组列表 
        
        'DEFAULT_GROUP'=>'Home', //默认分组

        'TMPL_FILE_DEPR'=>'_', //改变模板文件位置的显示（例如:Tpl/default/Home/Index/index.html，改变后为Tpl/default/Home/Index_index.html） 

        'URL_HTML_SUFFIX'=> '.shtml',  // URL伪静态后缀设置
        'THINK_APP_GROUP_LIST'=>'',//用think模板引擎分组列表 
        'TMPL_ENGINE_TYPE' => 'Smarty',//模板引擎类型
        'TMPL_CACHE_ON' => false,    //关闭页面的静态缓存
        //如果需要修改以下资源配置，请联系杨益
        //DB配置
        'DB_INFO' => array(
                //默认db资源
                'main' => array(
                    array(
                        'host' => '127.0.0.1',
                        'port' => '3306',
                        'user' => 'root',
                        'password' => 'root',
                        'db_name' => 'carpark'
                        ),
                    array(
                        'host' => '127.0.0.1',
                        'port' => '3306',
                        'user' => 'root',
                        'password' => 'root',
                        'db_name' => 'carpark'
                        )
                    ),
                'club' => array(
                        array(
                            'host' => '127.0.0.1',
                            'port' => '3306',
                            'user' => 'root',
                            'password' => 'root',
                            'db_name' => 'carpark'
                            ),
                        array(
                            'host' => '192.168.1.254',
                            'port' => '3306',
                            'user' => 'root',
                            'password' => 'root',
                            'db_name' => 'carpark'
                            )
                        ),

                ),
                //如果需要修改以下资源配置，请联系杨益
                //MC配置
                'MC_INFO' => array(
                        'main' => array(
                            'host' => '192.168.1.254',
                            'port' => '11211'
                            )
                        )
                );
