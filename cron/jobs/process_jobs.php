<?php

require_once(dirname(__DIR__) . '/bootstrap.php');

function job()
{
    print_r("\n");

    logg('Find next job');

    $jobs = jobs()
        ->where_status(0)
        ->where_null('deleted_at')
        ->where(
            fn ($q) => $q
                ->where_null('schedule_date')
                ->or_where('schedule_date', '<=', DB::raw('now()'))
        )
        ->take(5)
        ->get();

    foreach ($jobs as $job) {
        $args = secure_json_decode($job->data, true);
        $callback = null;

        logg('Start process job #' . $job->id . ' -> ' . $job->job);

        if (function_exists($job->job)) {
            $callback = $job->job;
        } elseif (class_exists($job->job)) {
            $callback = [$job->job, 'run'];
        }

        $started_at = date('Y-m-d H:i:s');
        $error = null;
        $status = 1;

        jobs()->update(compact('started_at'), $job->id);

        if (!$callback) {
            $error = 'Method or Class ' . $job->job . ' not exists!';
            $status = 2;
        } else {
            try {
                call_user_func_array($callback, $args);
            } catch (\Exception $e) {
                $error = $e->getMessage();
                $status = 2;
            }
        }

        $finished_at = date('Y-m-d H:i:s');

        jobs()
            ->update([
                'error' => $error,
                'status' => $status,
                'finished_at' => $finished_at,
            ], $job->id);

        logg('Finish process job #' . $job->id . ' -> ' . $job->job);
    }
}

$start_running = (int) date('YmdHis');
$loop = 1;
while (true) {
    logg('LOOP ' . $loop++);
    // Force restart after 5 minutes
    // if (((int) date('YmdHis') - $start_running) > 430) {
    //     logg('Force restart after 430 seconds');
    //     exit(0);
    // }
    job();
    sleep(10);

    // guaranteeSingleThread(__FILE__);
}
