<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Team / bundle invitation link lifetime
    |--------------------------------------------------------------------------
    |
    | Invitations expire at this many days after the leader submits. Expiry is
    | evaluated when the invitee opens the confirmation URL (no scheduler).
    |
    */
    'invitation_ttl_days' => (int) env('REGISTRATION_INVITATION_TTL_DAYS', 7),
];
