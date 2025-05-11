<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity_log extends Model
{
    protected $table = "activity_log";
    protected $primaryKey = 'activity_log_id'; 

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function biweekly()
    {
        return $this->belongsTo(Biweekly::class, 'biweekly_id');
    }

    public function products_production_stages()
    {
        return $this->belongsTo(Product_Production_Stage::class, 'product_production_stage_id');
    }
}
