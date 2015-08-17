<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Advert;
use App\Http\Requests;
use App\Http\Controllers\Controller;


class AdvertController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request, Advert $user)
    {
        return view('admin.advert.index', [
            'records' => $user->formatDuration(),
            'type'    => $request->input('type'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        //
        $adType = cons()->lang('advert.ad_type');
        $timeType = cons()->lang('advert.time_type');
        $appType = cons()->lang('advert.app_type');
        $type = $request->input('type');

        return view('admin.advert.create', [
            'ad'        => new Advert,
            'type'      => $type,
            'ad_type'   => $adType,
            'time_type' => $timeType,
            'app_type'  => $appType,
        ]);
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
        dd($request);
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
    public function edit(Request $request, $id)
    {
        dd(Advert::find($id));

        //
        return view('admin.advert.create', [
            'type' => $request->input('type'),
            'ad'   => Advert::find($id),
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
