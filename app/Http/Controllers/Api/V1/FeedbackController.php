<?php

namespace App\Http\Controllers\Api\V1;


use App\Models\Advert;
use App\Models\Feedback;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FeedbackController extends Controller
{

    public function index(Request $request)
    {
        $attributes = $request->only(['contact', 'content']);
        $content = trim(array_get($attributes, 'content'));
        if (empty($content)) {
            return $this->invalidParam('content', '内容不能为空');
        }

        if (Feedback::create($attributes)->exists) {
            return $this->created();
        }

        return $this->error('反馈提交失败');
    }
}
