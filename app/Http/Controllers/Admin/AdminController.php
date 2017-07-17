<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CreateAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Http\Requests\Admin\UpdatePasswordRequest;
use App\Models\Admin;
use App\Models\PayPassword;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function store(CreateAdminRequest $request)
    {
        if (Admin::create($request->all())->exists) {
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
     * @param $admin
     * @return \Illuminate\View\View
     */
    public function edit($admin)
    {
        $roles = Role::lists('name', 'id');

        return view('admin.admin.edit', ['user' => $admin, 'role' => $roles]);
    }

    /**
     * @param \App\Http\Requests\Admin\UpdateAdminRequest $request
     * @param $admin
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function update(UpdateAdminRequest $request, $admin)
    {
        if ($admin->fill($request->all())->save()) {
            return $this->success('修改成功', url('admin/admin'));
        }

        return $this->error('修改失败');
    }

    /**
     * @param $admin
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function destroy($admin)
    {
        return $admin->delete() ? $this->success('删除成功', url('admin/admin')) : $this->error('删除失败');
    }

    /**
     * 获取修改密码视图
     *
     * @return \Illuminate\View\View
     */
    public function getPassword()
    {
        return view('admin.admin.password');
    }

    /**
     * 修改当前管理员密码
     *
     * @param \App\Http\Requests\Admin\UpdatePasswordRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function putPassword(UpdatePasswordRequest $request)
    {
        $user = admin_auth()->user();
        //检查原密码是否正确
        if (Hash::check($request->input('old_password'), $user->password)) {
            //修改新密码
            if ($user->fill(['password' => $request->input('password')])->save()) {
                return $this->success('修改密码成功');
            }

            return $this->error('修改密码时遇到错误');
        }

        return $this->error('原密码错误');
    }

    /**
     * 获取修改密码视图
     *
     * @return \Illuminate\View\View
     */
    public function payPassword()
    {

        return view('admin.admin.password', ['pay' => true]);
    }

    /**
     * 修改支付密码
     *
     * @param \App\Http\Requests\Admin\UpdatePasswordRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function putPayPassword(UpdatePasswordRequest $request)
    {
        $password = PayPassword::first();
        if (is_null($password)) {
            return PayPassword::create(['password' => $request->input('password')]) ? $this->success('修改密码成功') : $this->error('修改码时出现问题');
        }

        //检查原密码是否正确
        if (Hash::check($request->input('old_password'), $password->password)) {
            //修改新密码
            if ($password->fill(['password' => $request->input('password')])->save()) {
                return $this->success('修改密码成功');
            }

            return $this->error('修改密码时遇到错误');
        }

        return $this->error('原密码错误');
    }

    /**
     * 批量删除记录
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteBatch(Request $request)
    {
        return Admin::destroy($request->input('checked')) ? $this->success('删除成功',
            url('admin/admin')) : $this->error('删除失败');
    }

    /**
     * 批量修改启用/禁用状态
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function putSwitch(Request $request)
    {
        $status = $request->input('status') ? cons('status.on') : cons('status.off');
        if (Admin::whereIn('id', $request->input('ids'))->update(['status' => $status])) {
            return $this->success('操作成功');
        }

        return $this->error('操作失败');
    }
}
