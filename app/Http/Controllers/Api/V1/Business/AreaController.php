<?php

namespace App\Http\Controllers\Api\V1\Business;


use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests;
use App\Models\Area;

class AreaController extends Controller
{
    protected $currUser;

    public function __construct()
    {
        $this->currUser = auth()->user();
    }

    /**
     * 添加地区
     *
     * @param \App\Http\Requests\Api\v1\CreateAreaRequest $request
     * @return mixed
     */
    public function store(Requests\Api\v1\CreateAreaRequest $request)
    {
        return $this->currUser->shop->areas()->create($request->all()) ? $this->success('添加成功') : $this->error('添加失败');
    }

    /**
     * 编辑地区
     *
     * @param \App\Http\Requests\Api\v1\CreateAreaRequest $request
     * @return mixed
     */
    public function update(Requests\Api\v1\CreateAreaRequest $request, $areaId)
    {
        try {
            $area = Area::find($areaId);
            if (!$area instanceof Area) {
                throw new \Exception('参数错误');
            }
            $area->fill($request->all())->save();
            return $this->success('修改成功');
        } catch (\Exception $e) {
            return $this->error('修改失败');
        }
    }

    /**
     * 删除地区
     *
     * @param $area
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function destroy($area)
    {
        return Area::destroy($area) ? $this->success('操作成功') : $this->error('操作失败');
    }

}
