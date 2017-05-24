<?php

namespace App\Models;

/**
 * 资产申请使用与审核
 * Class AssetReview
 *
 * @package App\Models
 */
class AssetApplyLog extends Model
{
    protected $table = 'asset_apply_log';
    protected $fillable = [
        'action',
        'asset_review_id',
        'opera_type',
        'operator',
    ];

    protected $appends = [];

    protected $hidden = [];
    
    public function opera()
    {
        return $this->belongsTo($this->opera_type,'operator');
    }
    
}
