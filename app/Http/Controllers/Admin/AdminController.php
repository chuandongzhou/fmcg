<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CreateAdminRequest;
use App\Http\Requests\Admin\UpdatePasswordRequest;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $admins = Admin::with('role')->get();

        return view('admin.admin.index', ['admins' => $admins]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $roles = Role::lists('name', 'id');

        return view('admin.admin.create', ['role' => $roles]);
    }

    /**
     * @param \App\Http\Requests\Admin\CreateAdminRequest $request
     * @param \App\Models\Admin $user
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function store(CreateAdminRequest $request, Admin $user)
    {
        if ($user->create($request->all())->exists) {
            return $this->success('添加成功', route('admin.admin.index'));
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
        $roles = Role::lists('name', 'id');
        $info = Admin::with('role')->find($id);

        return view('admin.admin.edit', ['user' => $info, 'role' => $roles]);
    }

    /**
     * @param \App\Http\Requests\Admin\CreateAdminRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function update(CreateAdminRequest $request, $id)
    {
        $user = Admin::find($id);
        if(!$user){
            return $this->error('该用户不存在');
        }
        if ($user->fill($request->all())->save()) {
            return $this->success('修改成功', route('admin.admin.index'));
        }

        return $this->error('修改失败');
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
       return Admin::destroy($id) ? $this->success('删除成功',route('admin.admin.index')):$this->error('删除失败');
    }

    public function changePassword(UpdatePasswordRequest $request)
    {
        //todo:获取当前用户的ID 然后修改密码。
    }
}
