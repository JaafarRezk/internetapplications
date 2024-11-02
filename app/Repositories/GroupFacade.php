<?php
namespace App\Repositories;

class GroupFacade extends Facade
{
    CONST aspects_map = array(
        'createGroup' => array('TransactionAspect', 'LoggingAspect'), 
        
    );

    public function __construct($message)
    {
        parent::__construct($message);
    }

    public function createGroup()
    {
        $group = $this->groupService->createGroup($this->message['bodyParameters']);
        $message = [
            'group_id' => $group->id,
            'users_ids' => [auth()->user()->id]
        ];
        // $groupAdded = $this->groupService->addUsersToGroup($message);
        return $group;
    }
}
