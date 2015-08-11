<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Contracts\Config\Repository as Config;

class HomeController extends Controller
{
    /**
     * 配置
     *
     * @var Config
     */
    protected $config;

    /**
     * 服务器信息
     *
     * @param \Illuminate\Contracts\Config\Repository $config
     * @return \Illuminate\View\View
     */
    public function getIndex(Config $config)
    {
        $this->config = $config;

        return view('admin.home.index', [
            'serverInfo' => $this->serverInfo(),
            'extensionsInfo' => $this->extensionsInfo(),
            'flodersInfo' => $this->flodersInfo()
        ]);
    }

    /**
     * 服务端信息列表
     *
     * @return array
     */
    protected function serverInfo()
    {
        return [
            ['name' => '操作系统', 'value' => php_uname()],
            ['name' => '运行环境', 'value' => app('request')->server('SERVER_SOFTWARE')],
            ['name' => 'PHP环境', 'value' => phpversion() . ' ( ' . php_sapi_name() . ' )'],
            [
                'name' => 'MYSQL版本',
                'value' => app('db')->connection()->getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION)
            ],
            ['name' => '上传大小限制', 'value' => ini_get('upload_max_filesize') . ' (PHP环境允许上传附件的大小限制)'],
            ['name' => '表单大小限制', 'value' => ini_get('post_max_size') . ' (会影响上传附件大小)'],
            ['name' => '执行时间限制', 'value' => ini_get('max_execution_time') . '秒 (0表示无限制)'],
            ['name' => '运行内存限制', 'value' => ini_get('memory_limit') . ' (允许PHP脚本使用的最大内存)'],
            ['name' => '剩余空间', 'value' => human_filesize(disk_free_space('.'))],
        ];
    }

    /**
     * PHP扩展信息列表
     *
     * @return array
     */
    protected function extensionsInfo()
    {
        return [
            [
                'name' => 'openssl',
                'is_exists' => function_exists('openssl_csr_new'),
                'type' => '框架',
                'detail' => 'openSSL 是一个强大的安全套接字层密码库，囊括主要的密码算法、常用的密钥和证书封装管理功能及SSL协议，并提供丰富的应用程序供测试或其它目的使用。'
            ],
            [
                'name' => 'mbstring',
                'is_exists' => function_exists('mb_strlen'),
                'type' => '框架',
                'detail' => 'mbstring 提供了针对多字节字符串的函数，能够帮你处理 PHP 中的多字节编码。 除此以外，mbstring 还能在可能的字符编码之间相互进行编码转换。'
            ],
            [
                'name' => 'gd2',
                'is_exists' => function_exists('gd_info'),
                'type' => '验证码',
                'detail' => 'gd2 提供了创建和处理包括 GIF， PNG， JPEG， WBMP 以及 XPM 在内的多种格式的图像。'
            ],
            [
                'name' => 'cURL',
                'is_exists' => function_exists('curl_setopt'),
                'type' => '推送',
                'detail' => 'PHP支持的由Daniel Stenberg创建的libcurl库允许你与各种的服务器使用各种类型的协议进行连接和通讯。'
            ],
            [
                'name' => 'fileinfo',
                'is_exists' => function_exists('finfo_open'),
                'type' => '上传',
                'detail' => 'fileinfo 中的函数通过在文件的给定位置查找特定的 魔术 字节序列 来猜测文件的内容类型以及编码。 虽然不是百分百的精确， 但是通常情况下能够很好的工作。'
            ],
        ];
    }

    /**
     * 目录权限信息列表
     *
     * @return array
     */
    protected function flodersInfo()
    {
        $checkFunctionList = ['read' => 'is_readable', 'write' => 'is_writable'];
        $checkFloderList = [
            storage_path() => ['read', 'write'],
            $this->config->get('path.upload') => ['read', 'write']
        ];

        $result = [];
        foreach ($checkFloderList as $path => $checkTypeList) {
            $attr = [];
            $relatePath = trim(str_replace([base_path(), '\\'], ['', '/'], $path), '\\/');
            if (!is_dir($path)) {
                $result[$relatePath] = false;
                continue;
            }

            foreach ($checkTypeList as $checkType) {
                $attr[$checkType] = call_user_func($checkFunctionList[$checkType], $path);
            }
            $result[$relatePath] = $attr;
        }

        return $result;
    }
}
