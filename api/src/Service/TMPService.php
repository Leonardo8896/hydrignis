<?php

namespace Leonardo8896\Hydrignis\Service;
use Leonardo8896\Hydrignis\Model\User;

class TMPService
{

    function __construct(
        private User $user,
        private array $data = []
    ){}

    static function load(User $user): TMPService
    {
        if(file_exists(__DIR__."/tmp/".$user->email.".tmp")) {
            $data = file_get_contents(__DIR__."/tmp/".$user->email.".tmp");
            $obj = json_decode($data);
            return new TMPService($user,$obj->user);
        } else {
            return new TMPService($user);
        }
    }

    
}