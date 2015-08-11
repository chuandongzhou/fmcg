<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CreateRoleRequest;
use App\Models\Node;
use App\Models\Role;
use Illuminate\Http\Request;


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

        return view('admin.role.index',['roles'=>$roles]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $node = Node::all();
        return view('admin.role.create', ['node' => $node]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\CreateRoleRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function store(CreateRoleRequest $request)
    {
        $data = $request->all();

        $role = Role::create(['name' => $data['name']]);
        $roleId = $role->id;
        $nodes = $request['node'];

        $role->nodes()->sync($nodes);

        return $this->success('添加成功');
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
        dd($role);
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
        $data = $request->all();
        $role = Role::create(['name' => $data['name'], 'id' => $id]);
        $nodes = $request['node'];
        if (!empty($nodes)) {
            $role->nodes()->detach()->sync($nodes);
        }


        return $this->success('添加成功');
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
    }}
