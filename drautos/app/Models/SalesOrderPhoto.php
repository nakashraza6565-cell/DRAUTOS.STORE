<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderPhoto extends Model
{
    protected $table = 'sale_order_photos';

    protected $fillable = [
        'sales_order_id',
        'filename',
        'original_name',
        'disk_path',
        'uploaded_by',
        'file_size',
        'mime_type',
    ];

    /**
     * The sales order this photo belongs to.
     */
    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    /**
     * The user who uploaded this photo.
     */
    public function uploader()
    {
        return $this->belongsTo(\App\User::class, 'uploaded_by');
    }

    /**
     * Human-readable file size.
     */
    public function getHumanFileSizeAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes < 1024)       return $bytes . ' B';
        if ($bytes < 1048576)    return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }
}
