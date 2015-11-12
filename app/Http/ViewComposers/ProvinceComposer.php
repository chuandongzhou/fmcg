<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/25
 * Time: 20:51
 */
namespace App\Http\ViewComposers;

use Carbon\Carbon;
use DB;
use Cache;
use Illuminate\Contracts\View\View;

class ProvinceComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View $view
     * @return void
     */
    public function compose(View $view)
    {
        $provinces = [];
        if (Cache::has('provinces')) {
            $provinces = Cache::get('provinces');
        }else {
            $expiresAt = Carbon::now()->addDays(10);
            $provinces = DB::table('address')->where('pid', 1)->lists('name', 'id');
            Cache::put('provinces', $provinces, $expiresAt);
        }

        $view->with('provinces', $provinces);
    }

}
