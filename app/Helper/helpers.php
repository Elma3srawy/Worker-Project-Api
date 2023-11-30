<?php

if (!function_exists('guard_name')) {
    function guard_name() :string
    {

        $guard = ""; // Initialize $guard with an empty string or any default value

        if (auth()->guard('admin')->check()) {
            $guard = "admin";
        } else if (auth()->guard('worker')->check()) {
            $guard = "worker";
        } else if (auth()->guard('client')->check()) {
            $guard = "client";
        }

         return $guard;

    }

}

?>
