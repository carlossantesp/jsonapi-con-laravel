<?php

use CloudCreativity\LaravelJsonApi\Facades\JsonApi;
use Illuminate\Support\Facades\Route;

JsonApi::register('v1')->routes(function($api){
    $api->resource('articles')->only('create')->middleware('auth');
    $api->resource('articles')->except('create');
});
