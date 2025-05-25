<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity_log extends Model
{
    protected $table = "activity_log";
    protected $primaryKey = 'activity_log_id'; 

    public function employee()
    {
        return $this->belongsTo(employee::class, 'employee_id');
    }

    public function biweekly()
    {
        return $this->belongsTo(biweekly::class, 'biweekly_id');
    }

    public function products_production_stage()
    {
        return $this->belongsTo(product_production_stage::class, 'production_stages_id');
    }
}
