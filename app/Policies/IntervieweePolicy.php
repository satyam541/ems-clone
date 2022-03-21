<?php

namespace App\Policies;

use App\User;
use App\Models\Interviewee;
use Illuminate\Auth\Access\HandlesAuthorization;

class IntervieweePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
   
        public function interviewee(User $user,Interviewee $interviewee)
        {
            return $user->hasPermission('Interviewee','interviewee');
        }

    
}
