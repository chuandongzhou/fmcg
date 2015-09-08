<?php

namespace App\Http\Controllers\Index;

use App\Models\Advert;
use App\Models\Goods;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use App\Http\Requests;

class HomeController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $type = $this->user->type;
        //热门商品
        $hotGoods = Goods::hot()->where('user_type', '>', $type)->take(16)->get();
        //热门商家
        $hotShops = Shop::ofHot()->whereHas('user', function ($q) use ($type) {
            $q->where('type', '>', $type);
        }
        )->get();
        $nowTime = Carbon::now();
        //广告
        $adverts = Advert::with('image')->where('type', cons('advert.type.index'))->OfTime($nowTime)->get();
        return view('index.index.index', ['hotGoods' => $hotGoods, 'hotShops' => $hotShops, 'adverts' => $adverts]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
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
    }
}
