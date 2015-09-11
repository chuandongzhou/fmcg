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
        $data = $request->all();
        $class = array_get(cons('model'), $data['type'], head(cons('model')));

        if ($data['status']) {
            $likeInfo = $class::find($data['id']);
            if (is_null($likeInfo)) {
                return $this->success(['status' => false]);
            }
            $likeInfo->likes()->firstOrCreate([]);
            return $this->success(['status' => true]);
        } else {
            $result = $class::find($data['id']);
            if (is_null($result)) {
                return $this->success(['status' => false]);
            }
            $likeInfo = $result->likes()->where('user_id', auth()->user()->id)->first();

            if (is_null($likeInfo)) {
                return $this->success(['status' => false]);
            }
            if ($likeInfo->delete()) {
                return $this->success(['status' => false]);
            }
            return $this->success(['status' => false]);
        }
    }
}