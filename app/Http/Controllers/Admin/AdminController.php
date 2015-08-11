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
        $admins = Admin::with('role')->paginate();

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
            return $this->success('添加账号成功');
        }

        return $this->error('添加账号时遇到错误');
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
        if (!$info) {
            return $this->error('该账号不存在', url('admin/admin'));
        }

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
        if (!$user) {
            return $this->error('该用户不存在');
        }
        if ($user->fill($request->all())->save()) {
            return $this->success('修改成功', url('admin/admin'));
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
        return Admin::destroy($id) ? $this->success('删除成功', url('admin/admin')) : $this->error('删除失败');
    }

    /**
     * 修改当前用户的密码
     *
     * @param \App\Http\Requests\Admin\UpdatePasswordRequest $request
     */
    public function changePassword(UpdatePasswordRequest $request)
    {
        //todo:获取当前用户的ID 然后修改密码。
    }

    /**
     * 批量删除记录
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteBatch(Request $request)
    {dd(111);
        dd($request->input('status'));
        return Admin::destroy($request->input('status')) ? $this->success('删除成功',
            url('admin/admin')) : $this->error('删除失败');
    }
}
