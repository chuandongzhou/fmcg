<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CreateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;


class WholesalersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        dd(User::where('group',config('constant.user_group.wholesalers'))->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin\user\create', ['group' => config('constant.user_group.wholesalers')]);
    }

    /**
     * @param \App\Http\Requests\Admin\CreateUserRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function store(CreateUserRequest $request)
    {
        if (User::Create($request->all())) {
            return $this->success('添加经销商成功', route('admin/wholesalers'));
        }
        return $this->error('添加用户时遇到错误');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
