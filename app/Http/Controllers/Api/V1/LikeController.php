<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/6
 * Time: 17:57
 */

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

class LikeController extends Controller
{

    /**
     * 收藏
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function putInterests(Request $request)
    {
        $type = $request->input('type');
        $likeTypes = array_keys(cons('like.type'));
        $relate = 'like' . ucfirst(in_array($type, $likeTypes) ? $type : head($likeTypes));
        $likeModels = cons('like.model');
        $model = array_get($likeModels, $type, head($likeModels));

        $user = auth()->user();
        $id = $request->input('id');
        $status = $request->input('status');

        if ($status) {
            $shop = $user->$relate()->where('id', $id)->first();
            if (!$shop) {
                $shop = $model::find($id);
                $shop ? $user->$relate()->attach($id) : null;
            }

            return $this->success($shop);
        } else {
            $user->$relate()->detach($id);
            return $this->success(null);
        }
    }
}