<?php

namespace App\Enums;

enum Permission: string
{
    case AccessDashboard = 'access dashboard';
    case ManagePosts = 'manage posts';
    case ManageProgrammes = 'manage programmes';
    case ManageGallery = 'manage gallery';
    case ManagePages = 'manage pages';
    case ManageEnquiries = 'manage enquiries';
    case ManageSubscribers = 'manage subscribers';
    case ManageSettings = 'manage settings';
    case ManageUsers = 'manage users';
    case ViewActivityLog = 'view activity log';
}
