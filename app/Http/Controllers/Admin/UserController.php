<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CreateUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Services\ChatService;
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
        $name = (string)$request->input('name');

        $users = User::where('type', array_get($types, $type, head($types)))->where('audit_status',
            cons('user.audit_status.pass'))->with('shop');

        if ($name) {
            $users = $users->where('user_name', 'Like', '%' . $name . '%');
        }

        return view('admin.user.index', [
            'users' => $users->paginate(),
            'filter' => [
                'type' => $type,
                'name' => $name
            ]
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
        $attributes = $request->all();
        $user = User::Create($attributes);
        if ($user->exists) {
            //插入商店
            $user->shop()->create(['name' => $user->user_name]);
            //Shop::create(['user_id' => $user->id]);

            return $this->success('添加用户成功');
        }

        return $this->error('添加用户时遇到错误');
    }

    /**
     *  Display the specified resource.
     *
     * @param $user
     * @return \Illuminate\View\View
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
            //更新远程
            $user->load('shop');
            (new ChatService)->usersHandle($user, true);
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
        //更新远程
        (new ChatService)->deleteUsers($user->shop_id);
        return $user->delete() ? $this->success('删除用户成功') : $this->error('删除用户时遇到错误');
    }

    /**
     *  账号审核页
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function audit()
    {
        $auditStatus = cons('user.audit_status');
        $users = User::whereIn('audit_status', array_except($auditStatus, 'pass'))->with('shop')->paginate();
        return view('admin.user.audit', [
            'users' => $users,
        ]);
    }

    /**
     * 账号审核处理
     *
     * @param \Illuminate\Http\Request $request
     * @param $user
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function auditUpdate(Request $request, $user)
    {
        $auditStatus = cons('user.audit_status');
        $status = (string)$request->input('status');
        $error = $request->input('reason');
        $result = in_array($status, $auditStatus) ? $status : head($auditStatus);

        if ($user->fill(['audit_status' => $result])->save()) {
            $status = $result == $auditStatus['pass'];
            if ($status) {
                $user->load('shop');
                (new ChatService)->usersHandle($user);
            }
            $this->_sendAuditSms($user, $status, $error);
            return $this->success('操作成功');
        }
        return $this->error('操作失败');
    }

    /**
     * 批量审核
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function multiAudit(Request $request)
    {
        $auditStatus = cons('user.audit_status');
        $status = (string)$request->input('status');
        $result = in_array($status, $auditStatus) ? $status : head($auditStatus);
        $userIds = $request->input('uid');
        if (is_null($userIds)) {
            return $this->error('请选择要审核的用户');
        }

        if (User::whereIn('id', $userIds)->update(['audit_status' => $result])) {
            $error = $request->input('reason');

            $users = User::whereIn('id', $userIds)->get(['user_name', 'backup_mobile', 'id']);
            foreach ($users as $user) {
                //更新远程
                $status = $result == $auditStatus['pass'];
                if ($status) {
                    $user->load('shop');
                    (new ChatService)->usersHandle($user);
                }
                $this->_sendAuditSms($user, $status, $error);
            }


            return $this->success('操作成功');
        }
        return $this->error('操作失败');
    }

    /**
     * 批量删除用户
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteBatch(Request $request)
    {
        $uids = (array)$request->input('uid');
        if (empty($uids)) {
            return $this->error('用户未选择');
        }
        return User::destroy($uids) ? $this->success('删除用户成功') : $this->error('用户删除时遇到错误');
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
     * 发送审核短信
     *
     * @param $user
     * @param $result
     * @param $reason
     */
    private function _sendAuditSms($user, $result, $reason)
    {
        app('pushbox.sms')->queue('audit', $user->backup_mobile,
            [
                'account' => $user->user_name,
                'result' => $result,
                'error' => $reason
            ]);
    }
}
