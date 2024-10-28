<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class File extends GenericModel
{
    use HasFactory;

    public $fillable = [
      'name',
      'path',
      'mime_type',
      'size',
      'checked',
      'version',
      'user_id',
      'file_holder_id',
    ];

    //public $hidden = ['path'];

    protected $casts = [
        'checked' => 'integer',
    ];
    public function deleteFileFSDAO(){
        return Storage::disk('public')->delete($this->path);
    }

    public function holder(){
        return $this->belongsTo(User::class,'file_holder_id');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_files','file_id')->withTimestamps();
    }
}
