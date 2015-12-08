<?php

namespace App\Http\Controllers\Admin;

use App\Models\BarcodeWithoutImages;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class BarcodeWithoutImagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $barcode = BarcodeWithoutImages::orderBy('id', 'DESC')->get();
        return view('admin.barcode.index', ['barcode' => $barcode]);
    }

    /**
     * 导出
     */
    public function export()
    {
        $barcode = BarcodeWithoutImages::orderBy('id', 'DESC')->get();
        if ($barcode->isEmpty()) {
            return $this->error('没有条形码，不能导出');
        }
        Excel::create('无图片的商品条码', function ($excel) use ($barcode) {
            $excel->sheet('Excel sheet', function ($sheet) use ($barcode) {
                $sheet->mergeCells('A1:C1');//merge for title
                $sheet->row(1, function ($row) {
                    $row->setFontSize(18);
                    $row->setAlignment('center');
                });
                $sheet->row(1, ['无图片的商品条码']);//add title
                $sub_titles = [
                    '条形码ID',
                    '条形码',
                    '添加时间时间',
                ];
                $sheet->setWidth([
                    'A' => 30,
                    'B' => 30,
                    'C' => 30,
                ]);

                $sheet->row('2', $sub_titles);
                foreach ($barcode as $code) {
                    $array = [
                        'id' => $code->id,
                        'barcode' => $code->barcode,
                        'created_at' => $code->created_at,
                    ];
                    $sheet->appendRow($array);
                }
            });

        })->export('xls');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        return BarcodeWithoutImages::destroy($id) ? $this->success('删除成功') : $this->success('删除失败');
    }

    /**
     * 批量删除记录
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function batch(Request $request)
    {
        return BarcodeWithoutImages::destroy($request->input('ids')) ? $this->success('删除成功',
            url('admin/admin')) : $this->error('删除失败');
    }
}
