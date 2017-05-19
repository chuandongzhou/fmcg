<?php

namespace App\Http\Controllers\Index\Personal;

use App\Http\Controllers\Index\Controller;
use App\Models\ChildUser;
use App\Models\IndexNode;
use App\Services\CategoryService;
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

        $childUsers = auth()->user()->childUser()->paginate();

        return view('index.personal.child-user-index', compact('childUsers'));
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\ChildUser $childUser
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(ChildUser $childUser)
    {
        $indexNodes = IndexNode::cache();

        $indexNodes =  CategoryService::unlimitForLayer($indexNodes);

        $userNodes = $childUser->nodes->pluck('id')->toArray();

        return view('index.personal.child-user-nodes', compact('indexNodes', 'userNodes', 'childUser'));
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
