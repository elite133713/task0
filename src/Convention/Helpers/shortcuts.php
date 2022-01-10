<?php

if (!function_exists('strEmpty')) {
    /**
     * @param string $string
     *
     * @return bool
     */
    function strEmpty(string $string): bool
    {
        return trim($string) === '';
    }
}
