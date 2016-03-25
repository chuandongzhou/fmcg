<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CreateRoleRequest;
use App\Models\Node;
use App\Models\Role;
use Illuminate\Http\Request;
use WeiHeng\Foundation\Tree;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $roles = Role::orderBy('id', 'DESC')->lists('name', 'id');

        return view('admin.role.index', ['roles' => $roles]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $nodes = Node::all()->toArray();
        return view('admin.role.role', [
            'nodes' => new Tree($nodes),
            'role' => new Role,
            'role_node' => []
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\CreateRoleRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function store(CreateRoleRequest $request)
    {
        $attributes = $request->all();

        $role = Role::create(['name' => $attributes['name']]);
        if ($role->exists) {
            $nodes = $attributes['node'];
            $role->nodes()->sync($nodes);
            return $this->success('添加角色成功');
        }
        return $this->error('添加角色时遇到问题');

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
        $role = Role::with('nodes')->find($id);
        $nodes = Node::all()->toArray();
        return view('admin.role.role', [
            'role' => $role,
            'role_node' => $role->nodes->pluck('pivot')->pluck('node_id')->toArray(),
            'nodes' => new Tree($nodes),
        ]);
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
        $role = Role::find($id);
        if (!$role) {
            $this->error('要修改的角色不存在');
        }
        $name = $request->input('name');
        if ($role->fill(['name' => $name])->save()) {
            $nodes = $request->input('node');
            if (!empty($nodes)) {
                $role->nodes()->detach();
                $role->nodes()->sync($nodes);
            }
            return $this->success('修改成功');
        }

        return $this->success('修改角色时出现错误');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        return Role::destroy($id) ? $this->success('删除成功') : $this->success('删除失败');
    }
}
