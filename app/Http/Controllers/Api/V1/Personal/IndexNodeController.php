<?php

namespace App\Http\Controllers\Api\V1\Personal;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Api\v1\CreateChildUserRequest;
use App\Http\Requests\Api\v1\UpdateChildUserRequest;
use App\Models\ChildUser;
use App\Models\IndexNode;
use App\Services\CategoryService;
use Gate;
use Illuminate\Http\Request;

class IndexNodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function index()
    {
        $indexNodes = IndexNode::cache();

        $indexNodes =  CategoryService::unlimitForLayer($indexNodes);

       return $this->success(compact('indexNodes'));
    }
}
