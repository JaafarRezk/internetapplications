<?php

namespace App\Services;

use App\Exceptions\CheckInException;
use App\Exceptions\fileDeletionException;
use App\Exceptions\FileInUseException;
use App\Exceptions\MaxFileSizeException;
use App\Exceptions\MaxNumFileException;
use App\Models\File;
use App\Models\Group;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FileService extends Service
{

  
    public function uploadFiles($bodyParameters)
    {
        if (count($bodyParameters) > 0 && count($bodyParameters['files']) > 0) 
        {
            $files = $bodyParameters['files'];
            foreach ($files as $key => $file) {

                $storagePath = Storage::disk('public')->put('documents', $file);

                $maxFileNumPerUser = env('MAX_FILE_NUM_PER_USER');
                $maxFileSizePerUserMB = env('MAX_FILE_SIZE_PER_USER');


                if ($maxFileNumPerUser !== null && $maxFileNumPerUser !== '') 
                {
                    $countFiles = File::where('user_id',auth()->user()->id)->count();
                    if($countFiles +1 >= $maxFileNumPerUser)
                    {
                        throw new MaxNumFileException('User Uploaded Maximum Number of Files !!');
                    }
                }
                if ($maxFileSizePerUserMB !== null && $maxFileSizePerUserMB !== '')
                {
                    $maxFileSizeBytes = $maxFileSizePerUserMB * 1024 * 1024;
                    if ($file->getSize() > $maxFileSizeBytes) 
                    {
                        throw new MaxFileSizeException('File Size Is Larger Than '. $maxFileSizePerUserMB .'MB !!');
                    }
                }
                $parameters = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $storagePath,
                    'mime_type' => $file->extension(),
                    'size' => $file->getSize(),
                    'checked' => '0',
                    'version' => '0',
                    'user_id' => auth()->user()->id,
                ];

                $returnFiles[$key] = File::createNewWithValidation($parameters);
            }

            return $returnFiles;
        } else {
            return null;
        }
    }

    public function getMyFiles()
    {
        $files = File::where('user_id', auth()->user()->id)->get()->toArray();
        if (count($files) > 0) {
            return $files;
        } else {
            return [];
        }
    }


}
