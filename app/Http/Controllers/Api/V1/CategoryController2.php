<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Attr;
use App\Models\Category;
use App\Services\AttrService;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * 文件上传接口
 *
 * @package App\Http\Controllers\Api\v1
 */
class Category2Controller extends Controller
{
    /**
     * Get Attr by Search
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function getAttr(Request $request, $id)
    {
        $data = $request->all();
        if (!isset($data['format'])) {
            $where = array_merge(['category_id' => $id], $data);
            $result = Attr::where($where)->lists('name', 'id');
            return $this->success($result);
        } else {
            $attrs = Attr::where('category_id', $id)->get([
                'id',
                'name',
                'pid'
            ])->toArray();
            $attrList = (new AttrService($attrs))->format();
            return $this->success($attrList);
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function getCategory(Request $request)
    {
        $pid = $request->get('pid');
        $categories = '';
        if ($pid) {
            $categories = Category::where('pid', $pid)->lists('name', 'id');
        } else {
            $post = $request->all();
            $pid = [
                0,
                $post['level1'],
                $post['level2']
            ];
            $result = Category::whereIn('pid', $pid)->select(['id', 'name', 'level'])->get();
            foreach ($result as $category) {
                $categories['level' . $category['level']][$category['id']] = $category['name'];
            }
        }

        return $this->success($categories);
    }

    /**
     * 获取所有分类信息
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getAllCategory(){
        return $this->success(CategoryService::unlimitForLayer(Category::all()->toArray()));
    }
}