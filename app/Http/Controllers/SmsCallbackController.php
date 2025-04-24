<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SmsCallbackController extends Controller
{
    public function __invoke(Request $request) {
        info($request);
    }
}
