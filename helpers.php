<?php

function fixslash($path)
{
    if (substr($path, -1) === '/') {
        $path = substr($path, 0, -1);
    }

    return $path;
}
