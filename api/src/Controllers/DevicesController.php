<?php

namespace Leonardo8896\Hydrignis\Controllers;
use Leonardo8896\Hydrignis\Model\User;

class DevicesController
{
    static public function index(User $user)
    {
        // Logic to handle device listing
        echo json_encode([
            'message' => 'teste',
        ]);
    }
}