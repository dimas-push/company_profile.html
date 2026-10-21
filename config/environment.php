<?php

function app_environment()
{
    $configuredEnvironment = getenv('EDUSCOPE_ENV');
    if (is_string($configuredEnvironment) && trim($configuredEnvironment) !== '') {
        return strtolower(trim($configuredEnvironment));
    }

    $host = isset($_SERVER['HTTP_HOST'])
        ? trim(strtolower((string) parse_url('http://' . $_SERVER['HTTP_HOST'], PHP_URL_HOST)), '[]')
        : '';
    $remoteAddress = isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : '';
    $localHosts = array('localhost', '127.0.0.1', '::1');
    $loopbackAddresses = array('127.0.0.1', '::1', '::ffff:127.0.0.1');

    if (in_array($host, $localHosts, true) && in_array($remoteAddress, $loopbackAddresses, true)) {
        return 'local';
    }

    return 'production';
}

function is_production_environment()
{
    return !in_array(app_environment(), array('local', 'development', 'dev'), true);
}
