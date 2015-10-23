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
class CategoryController extends Controller
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
        if (!isset($data['format']) || $data['format'] != 'true') {
            $categoryPid = Category::where('id', $id)->pluck('pid');

            $result = Attr::where('pid', 0)->whereIn('category_id', [$categoryPid, $id])->lists('name', 'attr_id');
            return $this->success($result);
        } else {
            $attrs = Attr::where('category_id', $id)->get([
                'attr_id',
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
        $pid = $request->input('pid');
        $categories = '';
        if ($pid) {
            $categories = Category::where('pid', $pid)->with('icon')->lists('name', 'id');
        } elseif (is_null($pid)) {
            $post = $request->all();
            $pid = [
                0,
                $post['level1'],
                $post['level2']
            ];
            $result = Category::whereIn('pid', $pid)->with('icon')->select(['id', 'name', 'level'])->get();
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
    public function getAllCategory()
    {
        return $this->success(CategoryService::unlimitForLayer(Category::with('icon')->get()->toArray()));
    }
}