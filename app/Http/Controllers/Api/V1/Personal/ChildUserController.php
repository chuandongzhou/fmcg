<?php

namespace App\Http\Controllers\Api\V1\Personal;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Api\v1\CreateChildUserRequest;
use App\Http\Requests\Api\v1\UpdateChildUserRequest;
use App\Models\ChildUser;
use Gate;
use Illuminate\Http\Request;

class ChildUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Api\v1\CreateChildUserRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function store(CreateChildUserRequest $request)
    {
        $attributes = $request->all();
        $user = auth()->user();
        $shopId = $user->shop_id;

        $attributes = array_add($attributes, 'shop_id', $shopId);

        return $user->childUser()->create($attributes)->exists ? $this->success('添加子账号成功') : $this->error('添加子账号时出现问题');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Bind index nodes
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\ChildUser $childUser
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function bindNode(Request $request, ChildUser $childUser)
    {
        if (Gate::denies('child-user', $childUser)) {
            return $this->forbidden('无权限修改');
        }
        $nodes = $request->input('nodes');

        if (empty($nodes)) {
            return $this->error('请选择权限');
        }
        if ($childUser->indexNodes()->sync($nodes)) {
            app('child.user')->cacheNodes($childUser, true);
            return $this->success('分配权限成功');
        }
        return $this->error('分配权限时出现问题');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }


    /**
     * change child-user status
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\ChildUser $childUser
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function status(Request $request, ChildUser $childUser)
    {
        if (Gate::denies('child-user', $childUser)) {
            return $this->forbidden('无权限修改');
        }
        $status = intval($request->input('status'));
        $childUser->status = $status;
        if ($childUser->save()) {
            if ($status) {
                return $this->success('操作成功');
            } else {
                return $this->success(null);
            }
        }
        return $this->error('操作失败');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Api\v1\UpdateChildUserRequest $request
     * @param \App\Models\ChildUser $childUser
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function update(UpdateChildUserRequest $request, ChildUser $childUser)
    {
        if (Gate::denies('child-user', $childUser)) {
            return $this->forbidden('无权限修改');
        }
        return $childUser->fill($request->only([
            'name',
            'phone',
            'password'
        ]))->save() ? $this->success('修改子账号成功') : $this->error('修改子账号时出现问题');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ChildUser
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function destroy(ChildUser $childUser)
    {
        if (Gate::denies('child-user', $childUser)) {
            return $this->forbidden('无权限删除');
        }
        return $childUser->delete() ? $this->success('删除子账号成功') : $this->error('删除子账号时出现问题');
    }
}
