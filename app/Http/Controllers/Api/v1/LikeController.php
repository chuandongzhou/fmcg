<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/6
 * Time: 17:57
 */

namespace App\Http\Controllers\Api\v1;

use App\Models\Like;
use Auth;
use Gate;

class LikeController extends Controller
{

    protected $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    /**
     * 添加收藏
     *
     * @param $attributes
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function postAdd($attributes)
    {
        $likeable = explode('_', $attributes);
        $likeType = cons('like.type');

        if (!array_get($likeType, $likeable[0])) {
            return $this->error('收藏失败');
        }

        $class = array_get(cons('model'), $likeable[0]);

        $likeInfo = $class::find($likeable[1]);

        $likeName = cons()->valueLang('like.type', array_get($likeType, $likeable[0]));

        if (is_null($likeInfo)) {
            return $this->error('收藏' . $likeName . '失败');
        }

        $likeResult =$likeInfo->likes()->firstOrCreate([]);
        if ($likeResult->exists) {
            return $this->success('收藏' . $likeName . '成功');
        }
        return $this->error('收藏' . $likeName . '失败');
    }


    public function deleteDelete($likeId)
    {
        $likeInfo = Like::findOrFail($likeId);
        if (Gate::denies('validate-user', $likeInfo)) {
            abort(403);
        }
        if ($likeInfo->delete()) {
            return $this->success('取消成功');
        }
        return $this->error('取消失败');

    }
}