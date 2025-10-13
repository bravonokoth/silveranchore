<?php

use Illuminate\Support\Facades\Broadcast;


Broadcast::channel('admin.notifications', function ($user) {
    return $user->hasRole('admin') || $user->hasRole('super_admin');
});

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


