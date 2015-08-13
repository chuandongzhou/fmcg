<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CreateUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $types = cons('user.type');
        $type = (string)$request->input('type');

        $users = User::where('type', array_get($types, $type, head($types)))->paginate();

        return view('admin.user.index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $type = (string)$request->input('type');
        $types = cons('user.type');
        $typeId = array_get($types, $type);
        if (empty($typeId)) {
            return $this->error('无法添加用户');
        }

        return view('admin.user.user', [
            'user' => new User,
            'typeId' => $typeId,
        ]);
    }

    /**
     * @param \App\Http\Requests\Admin\CreateUserRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function store(CreateUserRequest $request)
    {
        if (User::Create($request->all())->exists) {
            return $this->success('添加用户成功');
        }

        return $this->error('添加用户时遇到错误');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($user)
    {
        return view('admin.user.show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User $user
     * @return \Illuminate\View\View
     */
    public function edit($user)
    {
        return view('admin.user.user', [
            'user' => $user,
            'typeId' => $user->type,
        ]);
    }

    /**
     * @param \App\Http\Requests\Admin\UpdateUserRequest $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function update(UpdateUserRequest $request, $user)
    {
        if ($user->fill($request->all())->save()) {
            return $this->success('更新用户成功');
        }
        $this->error('更新时遇到错误');
    }

    /**
     * 删除用户
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function destroy($user)
    {
        if ($this->deleteUser($user)) {
            //TODO 删除其它信息
            return $this->success('删除用户成功');
        }
        return $this->error('删除用户时遇到错误');
    }

    /**
     * 批量删除用户
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteBatch(Request $request)
    {
        $post = $request->all();
        if (empty($post['uid'])) {
            return $this->error('用户未选择');
        }
        foreach ($post['uid'] as $id) {
            $user = User::find($id);

            if (!$this->deleteUser($user)) {
                return $this->error('用户' . $user->name . '删除时遇到错误');
            }
            return $this->success('删除用户成功');
        }
    }

    /**
     * 修改用户状态
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function putSwitch(Request $request)
    {
        $post = $request->all();
        if (empty($post['uid'])) {
            return $this->error('用户未选择');
        }
        if (User::whereIn('id', $post['uid'])->update(['status' => $post['status']])) {
            return $this->success('操作成功');
        }
        return $this->error('操作失败');
    }

    /**
     * 删除用户处理
     *
     * @param $user
     * @return bool
     */
    private function deleteUser($user)
    {
        if ($user->delete()) {
            //TODO 删除其它信息
            return true;
        }
        return false;
    }
}
