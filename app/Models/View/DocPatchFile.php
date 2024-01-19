<?php

namespace App\Models\View;

use Illuminate\Database\Eloquent\Model;

class DocPatchFile extends Model
{
    protected $table = 'doc_patch_file_view';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'SRI_Key',
        'PDF_Patch',
        'XML_Patch'
    ];
}
