<?php

if (!function_exists('divide_uid')) {
    /**
     * 分割uid
     *
     * @param int $uid
     * @param string $suffix
     * @return array
     */
    function divide_uid($uid, $suffix = '')
    {
        $uidStr = sprintf('%010u', $uid);

        return [
            substr($uidStr, 0, 4),
            substr($uidStr, 4, 4),
            substr($uidStr, -2) . $suffix
        ];
    }
}

if (!function_exists('upload_path')) {

    /**
     * 获取上传目录
     *
     * @param string|null $path
     * @param string $type
     * @return string
     */
    function upload_file($path = null, $type = '')
    {
        $configName = 'path.upload';
        $type && $configName .= '_' . $type;

        return config($configName) . $path;
    }
}
if (!function_exists('upload_url')) {

    /**
     * 获取上传目录URL
     *
     * @param string|null $path
     * @param string $type
     * @param bool $secure
     * @return string
     */
    function upload_url($path = null, $type = '', $secure = null)
    {
        $configName = 'path.upload';
        $type && $configName .= '_' . $type;

        $relatePath = str_replace(public_path(), '', config($configName));
        return str_replace('\\', '/', asset($relatePath . $path, $secure));
    }
}
if (!function_exists('upload_file_url')) {

    /**
     * 获取上传文件URL
     *
     * @param string|null $path
     * @param bool $secure
     * @return string
     */
    function upload_file_url($path = null, $secure = null)
    {
        $relatePath = str_replace(public_path(), '', config('path.upload_file'));

        return str_replace('\\', '/', asset($relatePath . $path, $secure));
    }

}
if (!function_exists('avatar_url')) {

    /**
     * 获取上传头像URL
     *
     * @param int $uid
     * @param int $size
     * @param bool $secure
     * @return string
     */
    function avatar_url($uid = 0, $size = 64, $secure = null)
    {
        $default = config('auth.default.avatar');
        $avatarPath = config('path.upload_avatar');
        $relatePath = str_replace(public_path(), '', $avatarPath);

        // 处理size
        array_key_exists($size, $default) || $size = 64;

        // 处理分割后的ID
        $path = implode('/', divide_uid($uid, "_{$size}.jpg"));

        // 处理缓存
        $mtime = @filemtime($avatarPath . $path);
        if (false !== $mtime) {
            return asset($relatePath . $path, $secure) . '?' . $mtime;
        }

        return asset($relatePath . $default[$size], $secure);
    }

}
if (!function_exists('human_filesize')) {

    /**
     * 格式化容量为易读字符串
     *
     * @param int $bytes
     * @param int $decimals
     * @return string
     */
    function human_filesize($bytes, $decimals = 2)
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f ", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }

}
if (!function_exists('cons')) {

    /**
     * @param null|string $key
     * @param mixed $default
     * @return \WeiHeng\Constant\Constant|mixed
     */
    function cons($key = null, $default = null)
    {
        $constant = app('constant');
        if (is_null($key)) {
            return $constant;
        }

        return $constant->get($key, $default);
    }
}
if (!function_exists('path_active')) {
    /**
     * 根据path设置菜单激活状态
     *
     * @param string|array $path
     * @param string $class
     * @return string
     */
    function path_active($path, $class = 'active')
    {
        $path = array_map(function ($p) {
            return explode('?', $p)[0];
        }, (array)$path);
        return call_user_func_array('\Request::is', (array)$path) ? $class : '';
    }
}
if (!function_exists('request_info')) {
    /**
     * Write some information with request to the log.
     *
     * @param  string $message
     * @param  mixed $context
     * @return bool
     */
    function request_info($message, $context = null)
    {
        $request = app('request');
        $qs = $request->getQueryString();
        $userId = intval(auth()->id());
        $message .= " [{$request->getClientIp()}] [$userId] {$request->getMethod()} {$request->getPathInfo()}" . ($qs ? '?' . $qs : '');

        return app('log')->info($message, [
            'context' => $context,
            'input' => $request->except(['password', 'password_confirmation']),
            'referer' => $request->server('HTTP_REFERER'),
            'ua' => $request->server('HTTP_USER_AGENT'),
        ]);
    }
}
if (!function_exists('multi_array_unique')) {
    /**
     * 二维数组去重
     *
     * @param array $data
     * @return array
     */
    function multi_array_unique($data = array())
    {
        $tmp = array();
        foreach ($data as $key => $value) {
            //把一维数组键值与键名组合
            foreach ($value as $key1 => $value1) {
                $value[$key1] = $key1 . '_|_' . $value1;//_|_分隔符复杂点以免冲突
            }
            $tmp[$key] = implode(',|,', $value);//,|,分隔符复杂点以免冲突
        }

        //对降维后的数组去重复处理
        $tmp = array_unique($tmp);

        //重组二维数组
        $newArr = array();
        foreach ($tmp as $k => $tmp_v) {
            $tmp_v2 = explode(',|,', $tmp_v);
            foreach ($tmp_v2 as $k2 => $v2) {
                $v2 = explode('_|_', $v2);
                $tmp_v3[$v2[0]] = $v2[1];
            }
            $newArr[$k] = $tmp_v3;
        }
        return $newArr;
    }
}
if (!function_exists('admin_auth')) {
    /**
     * Get the available admin auth instance.
     *
     * @return \Weiheng\Admin\Guard
     */
    function admin_auth()
    {
        return app('admin.auth');
    }
}
if (!function_exists('delivery_auth')) {
    /**
     * Get the available admin auth instance.
     *
     * @return \Weiheng\Delivery\Guard
     */
    function delivery_auth()
    {
        return app('delivery.auth');
    }
}
if (!function_exists('array_to_xml')) {
    /**
     * 数组转xml
     *
     * @param array $arr
     * @param \SimpleXMLElement $xml
     * @return \SimpleXMLElement
     */
    function array_to_xml(array $arr, SimpleXMLElement $xml)
    {
        foreach ($arr as $k => $v) {

            $attrArr = array();
            $kArray = explode(' ', $k);
            $tag = array_shift($kArray);

            if (count($kArray) > 0) {
                foreach ($kArray as $attrValue) {
                    $attrArr[] = explode('=', $attrValue);
                }
            }

            if (is_array($v)) {
                if (is_numeric($k)) {
                    array_to_xml($v, $xml);
                } else {
                    $child = $xml->addChild($tag);
                    if (isset($attrArr)) {
                        foreach ($attrArr as $attrArrV) {
                            $child->addAttribute($attrArrV[0], $attrArrV[1]);
                        }
                    }
                    array_to_xml($v, $child);
                }
            } else {
                $child = $xml->addChild($tag, $v);
                if (isset($attrArr)) {
                    foreach ($attrArr as $attrArrV) {
                        $child->addAttribute($attrArrV[0], $attrArrV[1]);
                    }
                }
            }
        }

        return $xml;
    }
}
if (!function_exists('xml_to_array')) {
    /**
     * xml转化为array
     *
     * @param $xmlData
     * @return mixed|\SimpleXMLElement
     */
    function xml_to_array($xmlData)
    {
        $array = simplexml_load_string($xmlData, null, LIBXML_NOCDATA);
        $array = json_decode(json_encode($array), true);
        return $array;
    }
}
if (!function_exists('array_key_to_value')) {
    /**
     * 格式化数组
     *
     * @param $array
     * @param $id
     * @param null $name
     * @return array
     */
    function array_key_to_value($array, $id, $name = null)
    {
        $newArray = [];
        foreach ($array as $arr) {
            $newArray[$arr[$id]] = is_null($name) ? $arr : $arr[$name];
        }
        return $newArray;
    }
}

