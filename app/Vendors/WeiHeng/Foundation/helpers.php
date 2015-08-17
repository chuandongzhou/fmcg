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

        return str_replace('\\', '/',asset($relatePath . $path, $secure));
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
