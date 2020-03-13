<?php

function initSession(): void
{
    session_start();
    session_regenerate_id();
}

function getSession(string $key, bool $delete = true)
{
    if (empty($_SESSION[$key])) {
        return null;
    }
    $return = $_SESSION[$key] ?? null;
    if ($delete) {
        unset($_SESSION[$key]);
    }
    return $return;
}

function saveSession(string $key, $value): void
{
    $_SESSION[$key] = $value;
}