<?php

namespace App\Enums;

enum EmailNotificationType: string
{
    case RegistrationSubmitted = 'registration_submitted';
    case RegistrationAccepted = 'registration_accepted';
    case RegistrationRejected = 'registration_rejected';
    case AttendanceConfirmed = 'attendance_confirmed';
    case TeamInvitation = 'team_invitation';
    case InvitationDeclinedByInvitee = 'invitation_declined_by_invitee';
    case TeamMemberInvitationAcceptedLeaderNotice = 'team_member_invitation_accepted_leader_notice';
    case TeamMemberInvitationDeclinedLeaderNotice = 'team_member_invitation_declined_leader_notice';
}
