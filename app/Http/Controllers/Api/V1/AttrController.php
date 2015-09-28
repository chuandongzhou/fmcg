<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/10
 * Time: 16:46
 */
namespace App\Http\Controllers\Api\V1;

use App\Models\Attr;
use Illuminate\Http\Request;


class AttrController extends Controller
{

    /**
     * 获取二级分类
     *
     * @param $pid
     * @return \WeiHeng\Responses\Apiv1Response
     */
    /*public function secondAttr(Request $request, $pid)
    {
        $categoryId = $request->input('category_id');

        $second = Attr::where(['pid' => $pid, 'category_id'=>$categoryId])->lists('name', 'attr_id');
        return $this->success($second);
    }*/
}