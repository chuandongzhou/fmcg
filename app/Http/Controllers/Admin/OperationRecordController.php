<?php

namespace App\Http\Controllers\Admin;

use App\Models\OperationRecord;

use App\Http\Requests;

class OperationRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

        $records = OperationRecord::orderBy('id', 'DESC')->paginate();
        return view('admin.operation.index', ['records' => $records]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.operation.operation-record', ['record' => new OperationRecord]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\CreateOperationRecordRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function store(Requests\Admin\CreateOperationRecordRequest $request)
    {
        if (OperationRecord::create($request->all())->exists) {
            return $this->success('添加记录成功');
        }
        return $this->error('添加记录时遇到问题');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $record
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function destroy($record)
    {
        if ($record->delete()) {
            return $this->success('删除记录成功');
        }
        return $this->error('删除记录时遇到问题');
    }
}
