<?php

namespace App\Http\Controllers\Admin;

use App\Models\Feedback;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex(Request $request)
    {
        $createdAt = new Carbon($request->input('feed_time'));
        if ($createdAt) {
            $feedbacks = Feedback::whereBetween('created_at', [$createdAt ,$createdAt->copy()->endOfDay()])->paginate();
        } else {
            $feedbacks = Feedback::paginate();
        }
        return view('admin.feedback.index', ['feedbacks' => $feedbacks, 'feedTime' => $createdAt]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function postHandle(Request $request)
    {
        $id = $request->input('id');
        if (!isset($id) || $id == 0) {
            return $this->error('处理失败');
        }
        $feedback = Feedback::find($id);

        if ($feedback->fill(['status' => 1])->save()) {
            return $this->success('处理反馈成功');
        }
        return $this->error('处理失败');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function deleteDestroy(Request $request)
    {
        $id = $request->input('id');
        if (!isset($id) || $id == 0) {
            return $this->error('删除失败');
        }
        $feedback = Feedback::find($id);

        if ($feedback->delete()) {
            return $this->success('删除反馈成功');
        }
        return $this->error('删除失败');
    }
}
