<?php
if (!function_exists('hashPWD')) {
    function hashPWD($password)
    {
        return bcrypt($password);
    }
} 