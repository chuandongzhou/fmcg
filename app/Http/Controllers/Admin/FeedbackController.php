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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex(Request $request)
    {
        $dates = $request->all();
        $beginDay = array_get($dates, 'begin_day');
        $endDay = array_get($dates, 'end_day');
        $feedbacks = Feedback::ofDates($dates)->paginate();

        return view('admin.feedback.index', ['feedbacks' => $feedbacks, 'beginDay' => $beginDay, 'endDay' => $endDay]);
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
