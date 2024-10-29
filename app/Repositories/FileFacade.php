<?php

namespace App\Repositories;

use App\Services\fileService;
use Exception;

class FileFacade extends Facade {

    const aspects_map = [
        'uploadFiles' => array('TransactionAspect', 'LoggingAspect','FileLoggingAspect'),
        'getMyFiles' => array('TransactionAspect', 'LoggingAspect'),
    ];

    public function __construct($message)
    {
        parent::__construct($message);
    }

    public function uploadFiles()
    {
        if (isset($this->message['bodyParameters'])) {
            $files = $this->fileService->uploadFiles($this->message['bodyParameters']);
            return $files;
        } else {
            throw new Exception('The bodyParameters key is missing in the message.');
        }
    }

    public function getMyFiles()
    {
        $files = $this->fileService->getMyFiles();
        return $files;
    }
}