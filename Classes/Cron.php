<?php
/**
 * Cron class
 */

namespace App;

use App\Entity\CronTask;

/**
 * This class manages Cron tasks to schedule / unschedule and run ready tasks.
 */
class Cron {    
    /**
     * Schedules a cron task that will be executed at the given timestamp
     *
     * @param  mixed $timestamp The timestamp at which the task will be run
     * @param  mixed $callback The callback function that will be executed
     * @param  mixed $params The arguments of the callback function
     * @param  mixed $interval The interval between each execution of the task
     * @return bool True if the task has been scheduled, false otherwise
     */
    public static function schedule($timestamp, $callback, $params = [], $interval = -1): bool
    {
        if(!is_numeric($timestamp) || $timestamp <= 0)
            return false;

        if(!is_array($params))
            $params = [$params];

        $cron = new CronTask();
        $cron->setTimestamp($timestamp)
            ->setCallback($callback)
            ->setParams($params)
            ->setInterval($interval);

        return $cron->save();
    }
    
    /**
     * Unschedules a cron task that corresponds to the given parameters
     *
     * @param  mixed $timestamp The timestamp at which the task is supposed to run
     * @param  mixed $callback The callback function that is supposed to run
     * @param  mixed $params The arguments of the callback function
     * @return bool True if a task exists and has been unscheduled, false otherwise
     */
    public static function unschedule($timestamp, $callback, $params = []): bool
    {
        if(!is_numeric($timestamp) || $timestamp <= 0)
            return false;

        if(!is_array($params))
            $params = [$params];

        $task = CronTask::exists($timestamp, $callback, $params);
        var_dump($task);

        if(!$task)
            return false;

        return $task->delete();
    }
    
    /**
     * Returns cron tasks that are ready to run
     *
     * @return CronTask[] An array of CronTasks 
     */
    private static function getReadyTasks(): array
    {
        $tasks = CronTask::getBy([
            'timestamp' => ['equal' => '<=', 'value' => time()]
        ]);

        return $tasks;
    }
    
    /**
     * Executes cron tasks that are ready to run
     *
     * @return bool Always returns true, except if an error occured
     */
    public static function executeTasks(): bool
    {
        $tasks = self::getReadyTasks();
        
        if(count($tasks) == 0)
            return true;

        foreach($tasks as $task) {
            $task->run();

            if($task->getInterval() >= 0)
                $task->setTimestamp(time() + $task->getInterval())->save();
            else
                $task->delete();
        }

        return true;
    }

}