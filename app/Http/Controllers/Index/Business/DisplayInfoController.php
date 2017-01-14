<?php

namespace App\Http\Controllers\Index\Business;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Index\Controller;
use PhpOffice\PhpWord\PhpWord;

class DisplayInfoController extends Controller
{
    protected $shop;

    public function __construct()
    {
        $this->shop = auth()->user()->shop;
    }

    /**
     * 陈列费发放情况
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $month = $request->input('month', (new Carbon)->format('Y-m'));
        $name = $request->input('name');
        $salesmanId = $request->input('salesman_id');
        $displayTypes = cons('salesman.customer.display_type');
        $customers = $this->shop->salesmenCustomer()
            ->ofSalesman($salesmanId)
            ->ofName($name)
            ->where('display_type', '<>', $displayTypes['no'])
            ->where(function ($query) use ($month) {
                $query->where('display_start_month', '<=', $month)->where('display_end_month', '>=', $month);
            })
            ->with([
                'displayList' => function ($query) use ($month) {
                    $query->where('month', $month);
                },
                'displayList.order.salesman',
                'displayList.order.order',
                'mortgageGoods'
            ])->paginate();

        $customers = $this->_formatCustomer($customers, $displayTypes);

        $salesmen = $this->shop->salesmen;

        return view('index.business.display-info', compact('customers', 'month', 'name', 'salesmanId', 'salesmen'));
    }

    /**
     * 陈列费发放导出
     *
     * @param \Illuminate\Http\Request $request
     */
    public function export(Request $request)
    {
        $month = $request->input('month', (new Carbon)->format('Y-m'));
        $name = $request->input('name');
        $salesmanId = $request->input('salesman_id');
        $displayTypes = cons('salesman.customer.display_type');
        $customers = $this->shop->salesmenCustomer()
            ->ofSalesman($salesmanId)
            ->ofName($name)
            ->where('display_type', '<>', $displayTypes['no'])
            ->where(function ($query) use ($month) {
                $query->where('display_start_month', '<=', $month)->where('display_end_month', '>=', $month);
            })
            ->with([
                'displayList' => function ($query) use ($month) {
                    $query->where('month', $month);
                },
                'displayList.order.salesman',
                'displayList.order.order',
                'mortgageGoods'
            ])->get();

        $customers = $this->_formatCustomer($customers, $displayTypes);

        $styleTable = array('borderSize' => 1, 'borderColor' => '999999');
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('仿宋');
        $phpWord->setDefaultFontSize(12);
        $phpWord->addTableStyle('table', $styleTable);
        $gridSpan2 = ['valign' => 'center', 'gridSpan' => 2];
        $gridSpan6 = ['valign' => 'center', 'gridSpan' => 6];
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
        $cellRowContinue = array('vMerge' => 'continue');

        $style = [
            'marginTop' => 500,
            'marginRight' => 0,
            'marginLeft' => 500,
            'marginBottom' => 0,
            'orientation' => 'landscape'
        ];

        foreach ($customers as $customer) {
            $section = $phpWord->addSection($style);
            $table = $section->addTable();

            $table->addRow();
            $table->addCell(1500, ['fill' => '#f2f2f2'])->addText('客户编号', ['bold' => true], array('align' => 'center'));
            $table->addCell(1500, ['fill' => '#f2f2f2'])->addText('店铺名称', ['bold' => true], array('align' => 'center'));
            $table->addCell(3500, ['fill' => '#f2f2f2'])->addText('联系人', ['bold' => true], array('align' => 'center'));
            $table->addCell(2500, ['fill' => '#f2f2f2'])->addText('联系电话', ['bold' => true], array('align' => 'center'));
            $table->addCell(4000, ['fill' => '#f2f2f2', 'valign' => 'center', 'gridSpan' => 2])->addText('营业地址',
                ['bold' => true], array('align' => 'center'));

            $table->addRow();
            $table->addCell(1500)->addText($customer->number, null, array('align' => 'center'));
            $table->addCell(1500)->addText($customer->name, null, array('align' => 'center'));
            $table->addCell(3500)->addText($customer->contact, null, array('align' => 'center'));
            $table->addCell(2500)->addText($customer->contact_information, null, array('align' => 'center'));
            $table->addCell(4000, $gridSpan2)->addText($customer->business_address_name, null,
                array('align' => 'center'));
            if ($customer->display_type == cons('salesman.customer.display_type.mortgage')) {
                $table = $section->addTable();
                $table->addRow();
                $table->addCell(13000, $gridSpan6)->addText('陈列费发放情况', null, array('align' => 'center'));
                $table->addRow();
                $table->addCell(1500)->addText('时间', null, array('align' => 'center'));
                $table->addCell(1500)->addText('商品名称', null, array('align' => 'center'));
                $table->addCell(3500)->addText('商品单位', null, array('align' => 'center'));
                $table->addCell(2500)->addText('应发商品数量', null, array('align' => 'center'));
                $table->addCell(2000)->addText('实发商品数量', null, array('align' => 'center'));
                $table->addCell(2000)->addText('剩余商品数量', null, array('align' => 'center'));

                foreach ($customer->mortgageGoods as $key => $mortgageGood) {
                    $table->addRow();
                    if ($key == 0) {
                        $table->addCell(0, $cellRowSpan)->addText($month, null, array('align' => 'center'));
                    } else {
                        $table->addCell(0, $cellRowContinue);
                    }
                    $table->addCell(0)->addText($mortgageGood->goods_name, null, array('align' => 'center'));
                    $table->addCell()->addText(cons()->valueLang('goods.pieces', $mortgageGood->pieces), null,
                        array('align' => 'center'));
                    $table->addCell()->addText($total = $mortgageGood->pivot->total, null, array('align' => 'center'));
                    $table->addCell()->addText($mortgageGood->used, null, array('align' => 'center'));
                    $table->addCell()->addText(bcsub($total, $mortgageGood->used), null, array('align' => 'center'));

                    $table->addRow();
                    $table->addCell()->addText('订单号', null, array('align' => 'center'));
                    $table->addCell()->addText('业务员', null, array('align' => 'center'));
                    $table->addCell()->addText('下单时间', null, array('align' => 'center'));
                    $table->addCell()->addText('状态', null, array('align' => 'center'));
                    $table->addCell()->addText('商品名称', null, array('align' => 'center'));
                    $table->addCell()->addText('实发商品数', null, array('align' => 'center'));

                    if (count($customer->orders)) {
                        foreach ($customer->orders as $order) {
                            foreach ($order['mortgages'] as $k => $item) {
                                $table->addRow();
                                if ($k == 0) {
                                    $table->addCell(0, $cellRowSpan)->addText($order['id'], null,
                                        array('align' => 'center'));
                                    $table->addCell(0, $cellRowSpan)->addText($order['salesmanName'], null,
                                        array('align' => 'center'));
                                    $table->addCell(0, $cellRowSpan)->addText($order['time'], null,
                                        array('align' => 'center'));
                                    $table->addCell(0, $cellRowSpan)->addText($order['status'], null,
                                        array('align' => 'center'));
                                } else {
                                    $table->addCell(0, $cellRowContinue);
                                    $table->addCell(0, $cellRowContinue);
                                    $table->addCell(0, $cellRowContinue);
                                    $table->addCell(0, $cellRowContinue);
                                }
                                $table->addCell()->addText($item['name'], null, array('align' => 'center'));
                                $table->addCell()->addText($item['used'], null, array('align' => 'center'));

                            }
                        }
                    } else {
                        $table->addRow();
                        $table->addCell(0, $gridSpan6)->addText('暂无', null, array('align' => 'center'));
                    }
                }
            } else {
                $table->addRow();
                $table->addCell(0, $gridSpan6)->addText('陈列费发放情况', null, array('align' => 'center'));
                $table->addRow();
                $table->addCell()->addText('时间', null, array('align' => 'center'));
                $table->addCell(0, $gridSpan2)->addText('应发现金金额(元)', null, array('align' => 'center'));
                $table->addCell()->addText('实发现金金额(元)', null, array('align' => 'center'));
                $table->addCell()->addText('剩余现金金额(元)', null, array('align' => 'center'));

                $table->addRow();
                $table->addCell()->addText($month, null, array('align' => 'center'));
                $table->addCell(0, $gridSpan2)->addText(number_format($fee = $customer->display_fee, 2), null,
                    array('align' => 'center'));
                $table->addCell()->addText( number_format($used =  $customer->displayLists->sum('used'), 2) , null,
                    array('align' => 'center'));
                $table->addCell()->addText(bcsub($fee, $used, 2), null,
                    array('align' => 'center'));

                $table->addRow();
                $table->addCell()->addText('订单号', null, array('align' => 'center'));
                $table->addCell()->addText('业务员', null, array('align' => 'center'));
                $table->addCell()->addText('下单时间', null, array('align' => 'center'));
                $table->addCell()->addText('状态', null, array('align' => 'center'));
                $table->addCell(0, $gridSpan2)->addText('实发现金金额（元）', null, array('align' => 'center'));

                if ($customer->displayLists->isEmpty()) {
                    $table->addRow();
                    $table->addCell(0, $gridSpan6)->addText('暂无', null, array('align' => 'center'));
                } else {
                    foreach ($customer->displayLists as $item) {
                        $table->addRow();
                        $table->addCell()->addText($item->salesman_visit_order_id, null, array('align' => 'center'));
                        $table->addCell()->addText($item->order->salesman_name, null, array('align' => 'center'));
                        $table->addCell()->addText($item->order->created_at, null, array('align' => 'center'));
                        $table->addCell()->addText($item->order->order_status_name, null, array('align' => 'center'));
                        $table->addCell(0, $gridSpan2)->addText($item->used, null, array('align' => 'center'));
                    }
                }

            }
        }

        $name = $month . '陈列费发放情况.docx';
        $phpWord->save($name, 'Word2007', true);

    }

    public function _formatCustomer($customers, $displayTypes)
    {
        $customers->each(function ($customer) use ($displayTypes) {
            if ($customer->display_type == $displayTypes['cash']) {
                //现金
                $customer->displayLists = $customer->displayList->filter(function ($item) {
                    return $item->mortgage_goods_id == 0;
                });
            }
            else {
                $displayLists = collect([]);
                $orders = [];
                foreach ($customer->displayList as $item) {
                    if ($item->mortgage_goods_id > 0) {
                        $displayLists->push($item);
                        $order = $item->order;
                        if (!isset($orders[$order->id])) {
                            $orders[$order->id] = [
                                'id' => $order->id,
                                'salesmanName' => $order->salesman_name,
                                'time' => $order->created_at,
                                'status' => $order->order_status_name
                            ];
                        }

                        $orders[$item->salesman_visit_order_id]['mortgages'][] = [
                            'name' => $item->mortgageGoods->goods_name,
                            'used' => (int)$item->used,
                            'pieces' => $item->mortgageGoods->pieces
                        ];
                    }
                }

                foreach ($customer->mortgageGoods as $mortgageGood) {
                    $goodsId = $mortgageGood->id;
                    $mortgageGood->used = $displayLists->filter(function ($item) use ($goodsId) {
                        return $item->mortgage_goods_id == $goodsId;
                    })->sum('used');
                }
                $customer->orders = array_values($orders);
            }
        });
        return $customers;
    }
}
