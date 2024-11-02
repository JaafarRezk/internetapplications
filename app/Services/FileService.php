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
use App\Models\FileVersion;

class FileService extends Service
{

    public function checkIn($id)
    {
        $file = File::fetchByIdWithCacheAndAuth($id);
    
        if ($file->checked == 0) {
            // Backup current file version
            $this->backupFileVersion($file);
    
            // Proceed with check-in
            $file->updateWithConditions([
                'checked' => 1,
                'version' => $file->version + 1,
                'file_holder_id' => auth()->user()->id
            ], [
                'id' => $id,
                'version' => $file->version,
            ]);
    
            return $file;
        } else {
            throw new FileInUseException('File is in use by someone else');
        }
    }
    

    public function bulkCheckIn($id_array)
    {
        $files = [];

            $ids_arr = preg_split ("/\,/", $id_array);

            foreach ($ids_arr as $id) {
                $file = $this->checkIn($id);
                array_push($files, $file);
            }

        return $files;
    }

    public function checkOut($bodyParameters)
    {
        $id = $bodyParameters["id"];
        $file = File::fetchByIdWithCacheAndAuth($id);
    
        if (isset($file) && $file->checked == 1) {
            // Backup current file version
            $this->backupFileVersion($file);
    
            $newFile = $bodyParameters['file'];
            $storagePath = Storage::disk('public')->put('documents', $newFile);
            $file->deleteWithCache();
    
            $file->updateWithConditions([
                'checked' => 0,
                'version' => 0,
                'path' => $storagePath,
                'file_holder_id' => null
            ], [
                'id' => $id,
            ]);
            return $file;
        } else {
            return null;
        }
    }

    protected function backupFileVersion($file)
{
    $backupData = [
        'file_id' => $file->id,
        'name' => $file->name,
        'path' => $file->path,
        'mime_type' => $file->mime_type,
        'size' => $file->size,
        'version_number' => $file->version,
    ];

    // Create a new backup entry in the file_versions table
    FileVersion::create($backupData);
}



public function restoreFileVersion($versionId)
{
    $version = FileVersion::find($versionId);

    if ($version) {
        // Update the original file with the version data
        $file = File::find($version->file_id);
        $file->update([
            'name' => $version->name,
            'path' => $version->path,
            'mime_type' => $version->mime_type,
            'size' => $version->size,
            'checked' => 0, // Optionally reset checked status
            'version' => $version->version_number
        ]);

        return $file;
    }

    throw new Exception('Version not found');
}



    public function freeFiles($files)
    {
        $res = [];
        foreach ($files as $file) {
            $r = $file->updateWithConditions(['checked' => 0, 'version' => 0], []);
            array_push($res, $r);
        }
        return !in_array(null, $res);
    }

    public function getMyFiles()
    {
        $files = File::where('user_id', auth()->user()->id)->get()->toArray();   //todo this should be DAO
        if (count($files) > 0) {
            return $files;
        } else {
            return [];
        }
    }

    public function uploadFiles($bodyParameters)
    {
        if (count($bodyParameters) > 0 && count($bodyParameters['files']) > 0) {
            $files = $bodyParameters['files'];
            foreach ($files as $key => $file) {

                $storagePath = Storage::disk('public')->put('documents', $file);    //todo this should be DAO

                $maxFileNumPerUser = env('MAX_FILE_NUM_PER_USER');
                $maxFileSizePerUserMB = env('MAX_FILE_SIZE_PER_USER');


                if ($maxFileNumPerUser !== null && $maxFileNumPerUser !== '') {
                    $countFiles = File::where('user_id',auth()->user()->id)->count();
                    if($countFiles +1 >= $maxFileNumPerUser){
                        throw new MaxNumFileException('User Uploaded Maximum Number of Files !!');
                    }
                }
                if ($maxFileSizePerUserMB !== null && $maxFileSizePerUserMB !== '') {
                    $maxFileSizeBytes = $maxFileSizePerUserMB * 1024 * 1024;
                    if ($file->getSize() > $maxFileSizeBytes) {
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

    public function removeFiles($id_array)
    {
        $all_deleted = [];
        $arr = [];
        $id_array = explode(',',$id_array);
        if (count($id_array) > 0) {

            foreach ($id_array as $file_id) {

                $file = File::fetchByIdWithCacheAndAuth($file_id);

                array_push($arr, $file);

                if ($file) {
                    $res = $file->deleteWithCache();
                    $file->deleteWithCache();
                    array_push($all_deleted, $res);
                }
            }
            if (in_array(false, $all_deleted)) {
                throw new fileDeletionException('Unable to delete file');
            } else {
                return 1;
            }
        } else {
            return null;
        }
    }

    public function readFile($id)
    {
        $file = File::fetchByIdWithCacheAndAuth($id);
        $file->path = storage_path('app/public/'.$file->path);
        $file->path = str_replace('\\', '/', $file->path);

        return $file;
    }

}
