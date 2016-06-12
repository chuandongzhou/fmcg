<?php

namespace App\Http\Controllers\Api\V1;


use App\Services\AttrService;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Cache;

/**
 * 文件上传接口
 *
 * @package App\Http\Controllers\Api\v1
 */
class CategoryController extends Controller
{
    protected $categories;

    public function __construct()
    {
        $this->categories = CategoryService::getCategories();
    }

    /**
     * Get Attr by Search
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function getAttr(Request $request, $id)
    {
        $data = $request->all();
        $attrService = new AttrService;

        if (!isset($data['format']) || $data['format'] != 'true') {
            $attrs = $attrService->getAttrsByCategoryId($id);
            $categoryPid = $this->categories[$id]['pid'];
            $attrs = array_merge($attrService->getAttrsByCategoryId($id),
                $attrService->getAttrsByCategoryId($categoryPid));
            $result = [];
            foreach ($attrs as $attr) {
                if ($attr['pid'] == 0) {
                    $result[$attr['attr_id']] = $attr['name'];
                }
            }
            return $this->success($result);
        } else {
            $attrs = $attrService->getAttrsByCategoryId($id);
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
        $categoriesTemp = $this->categories;
        $categories = [];
        if ($pid) {
            foreach ($categoriesTemp as $category) {
                if ($category['pid'] == $pid) {
                    $categories[$category['id']] = $category['name'];
                }
            }
        } elseif (is_null($pid)) {
            $post = $request->all();
            $pids = [
                0,
                $post['level1'],
                $post['level2']
            ];
            foreach ($categoriesTemp as $category) {
                if (in_array($category['pid'], $pids)) {
                    $categories['level' . $category['level']][$category['id']] = $category['name'];
                }
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
        return $this->success(CategoryService::unlimitForLayer($this->categories, 0, 'child', false));
    }

}