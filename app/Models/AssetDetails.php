<?php

namespace App\Models;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Model;

class AssetDetails extends Model
{
    protected $table = 'asset_details';
    protected $guarded = ['id'];
    // public $image_path = "upload/bill/";

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    // public function getImagePath()
    // {
    //     $img_src = url($this->image_path . $this->upload_image);
    //     if (file_exists(public_path($this->image_path . $this->upload_image))) {
    //         return $img_src;
    //     }
    // }
}
