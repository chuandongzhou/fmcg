<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CreateUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Input;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $getGroup = Input::get('group');
        $getGroup = in_array($getGroup, array_keys(cons('user.type'))) ? $getGroup : array_keys(cons('user.type'))[0];
        $group = cons('user.type.' . $getGroup);
        $users = User::where('group', $group)->get();
        return view('admin\user\index', ['users' => $users, 'group' => $group]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $getGroup = Input::get('group');
        $getGroup = in_array($getGroup, array_keys(cons('user.type'))) ? $getGroup : array_keys(cons('user.type'))[0];
        $group = cons('user.type.' . $getGroup);
        return view('admin\user\user', ['group' => $group]);
    }

    /**
     * @param \App\Http\Requests\Admin\CreateUserRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function store(CreateUserRequest $request)
    {
        if (User::Create($request->all())) {
            $redirect = $request->input('group') == cons('user.type.wholesalers') ? 'wholesalers' : 'retailer';
            return $this->success('添加用户成功', url('admin/user?group=' . $redirect));
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

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $userInfo
     * @return Response
     */
    public function edit($userInfo)
    {
        return view('admin\user\user', ['user' => $userInfo, 'group' => $userInfo->group]);
    }

    /**
     * @param \App\Http\Requests\Admin\UpdateUserRequest $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function update(UpdateUserRequest $request, $user)
    {
        if ($user->fill($request->all())->save()) {
            $redirect = $request->input('group') == cons('user.type.wholesalers') ? 'wholesalers' : 'retailer';
            return $this->success('添加用户成功', url('admin/user?group=' . $redirect));
        }
        $this->error('更新时遇到错误');
    }

    /**
     * 删除用户
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function destroy($user)
    {
        if ($user->delete()) {
            $redirect = $user->group == cons('user.type.wholesalers') ? 'wholesalers' : 'retailer';
            return $this->success('删除用户成功', url('admin/user?group=' . $redirect));
        }
        return $this->error('删除用户时遇到错误');
    }
}
