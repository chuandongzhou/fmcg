<?php
/**
 * Created by PhpStorm.
 * User: wh
 * Date: 2015/9/11
 * Time: 14:32
 */

namespace App\Services;


class ExportWordService
{
    /**
     * 开启输出缓冲,文件格式信息写入缓冲区
     */
    public function start()
    {
        ob_start();
        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office"
                xmlns:w="urn:schemas-microsoft-com:office:word"
                xmlns="http://www.w3.org/TR/REC-html40">';
    }

    /**
     * 获取缓冲区信息并写入到指定文件中
     *
     * @param $path
     */
    public function save($path)
    {

        echo "</html>";
        $data = ob_get_contents();
        ob_end_clean();

        $this->wirtefile($path, $data);
        ob_flush();//每次执行前刷新缓存
        flush();//刷新PHP程序的缓冲，,阻止输出发送到客户端浏览器
    }

    /**
     * 写入数据
     *
     * @param $fn
     * @param $data
     */
    public function wirtefile($fn, $data)
    {
        $fp = fopen($fn, "wb");
        fwrite($fp, $data);
        fclose($fp);
    }
}