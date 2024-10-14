<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel("v1.samples.{id}", function ($user, $id) {

    return true;
});
