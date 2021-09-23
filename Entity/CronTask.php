<?php
/**
 * CronTask class
 */

namespace App\Entity;

use App\Entity;

/**
 * This class represents a planned task that will run one or several times. When the given timestamp will be reached, the task will be able to run a callback function, with given arguments.
 */
class CronTask extends Entity {
        
    /**
     * The timestamp at which the task will run
     *
     * @Attribute(type="int", length=11, not_null=true)
     * 
     * @var int
     */
    protected $timestamp;

    /**
     * The name of the callback function
     *
     * @Attribute(type="varchar", length=255, not_null=true)
     * 
     * @var string
     */
    protected $callback;

    /**
     * The arguments of the callback function
     *
     * @Attribute(type="text", not_null=true)
     * 
     * @var string
     */
    protected $params;

    /**
     * The interval between each execution of the task
     *
     * @Attribute(type="int", length=11, not_null=true, default=-1)
     * 
     * @var int
     */
    protected $interval_;

    /**
     * A key that identifies the callback, the arguments and the timestamp
     *
     * @Attribute(type="varchar", length=255, unique=true, not_null=true)
     * 
     * @var string
     */
    protected $key_;

    public static $table = 'cron_task';
    
    /**
     * Constructor - Calls the parent constructor to load the object if an ID is provided
     *
     * @param  int $id ID of the entity to load, or null if it's a new one
     * @return void
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    /**
     * Returns the timestamp at which the task will run
     * 
     * @return int The timestamp at which the task will run
     */ 
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * Sets the timestamp at which the task will run
     *
     * @param int $timestamp The timestamp at which the task will run
     * @return  self The instance of the CronTask
     */ 
    public function setTimestamp($timestamp): CronTask
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Returns the name of the callback function
     * 
     * @return string The name of the callback function
     */ 
    public function getCallback(): string
    {
        return $this->callback;
    }

    /**
     * Sets the name of the callback function
     * 
     * If the function is in a class, use the format "Class::method"
     *
     * @param string $callback The name of the callback function
     * @return  self The instance of the CronTask
     */ 
    public function setCallback($callback): CronTask
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * Returns the arguments of the callback function
     * 
     * @return array The arguments of the callback function
     */ 
    public function getParams(): array
    {
        return json_decode($this->params, true);
    }

    /**
     * Sets the arguments of the callback function
     *
     * @param array $params The arguments of the callback function
     * @return  self The instance of the CronTask
     */ 
    public function setParams($params): CronTask
    {
        $this->params = json_encode($params);

        return $this;
    }

    /**
     * Returns the interval between each execution of the task. If it is equal to -1, the task will run only once
     * 
     * @return int The interval between each execution of the task
     */ 
    public function getInterval(): int
    {
        return $this->interval_;
    }

    /**
     * Sets the interval between each execution of the task. If set to -1, the task will run only once
     *
     * @param int $interval The interval between each execution of the task
     * @return  self The instance of the CronTask
     */ 
    public function setInterval($interval): CronTask
    {
        $this->interval_ = $interval;

        return $this;
    }
    
    /**
     * Runs the callback function with the task parameters
     *
     * @return void
     */
    public function run(): void
    {
        if(is_callable($this->callback))
            call_user_func($this->callback, ...$this->getParams());
    }
    
        
    /**
     * Checks if a task corresponding to the given timestamp, callback and params is already planned
     *
     * @param  int $timestamp The timestamp
     * @param  string $callback The callback
     * @param  array $params The arguments
     * @return CronTask|null Returns a CronTask object if a corresponding task exists, null otherwise
     */
    public static function exists($timestamp, $callback, $params): ?CronTask
    {
        $key = md5($timestamp.$callback.json_encode($params));
        var_dump($key);
        return self::getByKey($key);
    }
    
    /**
     * Searches for the task corresponding to the given key
     *
     * @param  string $key The key to search
     * @return CronTask|null Returns a CronTask object if the key has been found, null if no task corresponds to the key
     */
    private static function getByKey($key): ?CronTask
    {
        $task = self::getBy(['key_' => $key]);

        return count($task) > 0 ? $task[0] : null;
    }
    
    /**
     * Override of Entity::save(), to create the unique key before saving the task
     * 
     * @see Entity::save()
     *
     * @return bool Returns true if the entity is saved, false otherwise
     */
    public function save(): bool
    {
        $this->key_ = md5($this->timestamp.$this->callback.$this->params);

        return parent::save();
    }
}