<?php


function logg($mixed)
{
    if (is_array($mixed) || is_object($mixed)) {
        $mixed = json_encode($mixed);
    }
    echo date('Y-m-d H:i:s') . ' - ' . getmygid() . ' - ' . print_r($mixed, true) . "\n";
}

function removeExtraWhiteSpaces($string)
{
    $string = preg_replace('/\t+/', ' ', $string);
    $string = preg_replace('/\s+/', ' ', $string);
    return trim($string);
}

function scriptIsRunning($script)
{
    exec('echo $OSTYPE', $os);
    $os = implode(' ', $os);

    $script = basename($script);
    $cmd = "ps aux | grep -i '" . (env('DEV_MODE') ? 'dev-.*' : '') . "cron.*{$script}' | grep -v grep | grep -v jailshell";
    exec($cmd, $result);

    if (empty($result) && preg_match('/darwin/i', $os)) {
        $cmd = "ps -C | grep -i 'cron.*{$script}' | grep -v grep | grep -v jailshell";
        exec($cmd, $result);

        return array_map(function ($pid) {
            return explode(' ', removeExtraWhiteSpaces($pid))[0];
        }, $result);
    }

    return array_map(function ($pid) {
        return explode(' ', removeExtraWhiteSpaces($pid))[1];
    }, $result);
}

function guaranteeSingleThread($script)
{
    $pid_ids = scriptIsRunning($script);

    if (count($pid_ids) > 1) {
        array_pop($pid_ids);
        logg('This script is already running. Current PID ' . implode(', ', $pid_ids));
        exit(0);
    }
}
