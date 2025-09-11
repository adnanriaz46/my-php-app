<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MyBuyerListBuyer extends Model
{
    protected $fillable = [
        'my_buyer_list_id',
        'investor_identifier',
        'mrp_fullstreet',
        'mrp_city',
        'mrp_state',
        'mrp_zip',
        'mrp_sales_price',
        'mrp_purchase',
        'mrp_beds',
        'mrp_bath',
        'mrp_sqft',
        'MailingFullStreetAddress',
        'MailingCity',
        'MailingState',
        'MailingZIP5',
        'skip_trace_result_id',
        'last_traced_by_user_id',
        'last_traced_at',
        'last_trace_success',
        'last_trace_source'
    ];

    protected $casts = [
        'mrp_sales_price' => 'decimal:2',
        'mrp_purchase' => 'date',
        'last_traced_at' => 'datetime',
        'last_trace_success' => 'boolean',
    ];

    public function list()
    {
        return $this->belongsTo(MyBuyerList::class, 'my_buyer_list_id');
    }

    public function skipTraceResult()
    {
        return $this->belongsTo(SkipTraceResult::class, 'skip_trace_result_id');
    }
}
