<?php

namespace App\Repositories;

use App\Services\FileService;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;

class FileFacade extends Facade
{
    const aspects_map = array(
        'getMyFiles' => array('TransactionAspect', 'LoggingAspect'),
        'checkIn' => array('TransactionAspect', 'LoggingAspect','FileLoggingAspect'),
        'bulkCheckIn' => array('TransactionAspect', 'LoggingAspect','FileLoggingAspect'),
        'checkOut' => array('TransactionAspect', 'LoggingAspect','FileLoggingAspect'),
        'uploadFiles' => array('TransactionAspect', 'LoggingAspect','FileLoggingAspect'),
        'removeFiles' => array('TransactionAspect', 'LoggingAspect'),
        'readFile' => array('TransactionAspect', 'LoggingAspect'),
    );

    public function __construct($message)
    {
        parent::__construct($message);
    }

    public function checkIn()
    {
        $id = $this->message['urlParameters']['id'];
        $file = $this->fileService->checkIn($id);
        return $file;
    }

    public function checkOut()
    {

        $bodyParameters = $this->message['bodyParameters'];
        $file = $this->fileService->checkOut($bodyParameters);
        return $file;
    }

    public function getMyFiles()
    {
        $files = $this->fileService->getMyFiles();
        return $files;
    }

    public function bulkCheckIn()
    {
        $id_array = $this->message['bodyParameters'];
        $file = $this->fileService->bulkCheckIn($id_array['file_ids']);
        return $file;
    }

    public function uploadFiles()
    {
        $files = $this->fileService->uploadFiles($this->message['bodyParameters']);
        return $files;
    }

    public function removeFiles()
    {

        $id_array = $this->message['bodyParameters'];

        $files = null;

        $res = $this->fileService->bulkCheckIn($id_array['file_ids']);

        if ($res != null)
            $files = $this->fileService->removeFiles($id_array['file_ids']);

        return $files;
    }


    public function readFile()
    {
        $id = $this->message['urlParameters']['id'];
        $res = $this->fileService->readFile($id);

        return $res;
    }

}
