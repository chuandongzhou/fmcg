<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2017/12/5
 * Time: 10:05
 */
namespace WeiHeng\ExcelImport;

use Maatwebsite\Excel\Facades\Excel;

class ExcelImport
{

    protected $errorMsg;

    /**
     * 上传文件获取地址
     *
     * @param $file
     * @return bool|string
     */
    protected function uploadExcelToGetFilePath($file)
    {
        $tempPath = config('path.upload_temp');

        // 判断文件是否有效
        if (!is_object($file) || !$file->isValid()) {
            $this->setErrorMsg('无效的文件');
            return false;
        }
        $ext = $file->getClientOriginalExtension();

        if (!in_array($ext, cons('goods.import_allow_ext'))) {
            $this->setErrorMsg('文件类型错误');
            return false;
        }

        $path = date('Y/m/d/');
        $name = uniqid() . '.' . $ext;

        try {
            // 移动到目录
            $file->move($tempPath . $path, $name);
        } catch (FileException $e) {
            info($e);

            $this->setErrorMsg('上传失败');
            return false;
        }
        return $tempPath . $path . $name;
    }

    /**
     * 获取文件
     *
     * @param $path
     * @param $skip
     * @return mixed
     */
    public function getData($path, $skip)
    {
        $results = Excel::selectSheetsByIndex(0)->load($path, function ($reader) {
        })->skip($skip)->toArray();
        @unlink($path);
        return $results;
    }

    /**
     * 获取文件
     *
     * @param $file
     * @param int $skip
     * @return bool|mixed
     */
    public function fetchContent($file, $skip = 1)
    {
        $path = $this->uploadExcelToGetFilePath($file);
        if (!$path) {
            return false;
        }
        $data = $this->getData($path, $skip);
        return $data;
    }

    /**
     * 设置错误信息
     *
     * @param $msg
     */
    public function setErrorMsg($msg)
    {
        $this->errorMsg = $msg;
    }

    /**
     * 获取错误信息
     *
     * @return mixed
     */
    public function getErrorMsg()
    {
        return $this->errorMsg;
    }
}