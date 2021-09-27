<?php
/**
 * Session class
 */

namespace App;

use App\UserInterface;

/**
 * This class is a wrapper for the superglobal $_SESSION variable. It can be used to store data in the session. 
 * Methods were created to manage a simple login / logout system. The User class must implement UserInterface
 * 
 * @see UserInterface
 */
class Session {
    /**
     * The name of the user class (the class that implements UserInterface)
     * This variable serves as a cache variable. Thus it can be empty. To get the class name, please use Session::getUserClass()
     * 
     * @see Session::getUserClass()
     * 
     * @var string
     */
    private static $userClass = '';
    
    /**
     * Returns the value of $key stored in the session
     *
     * @param  string $key The name of the desired session value
     * @return mixed|null The session value if it exists, null if it doesn't
     */
    public static function get($key)
    {
        if(isset($_SESSION[$key]))
            return $_SESSION[$key];

        return null;
    }
    
    /**
     * Sets a value in the session
     * 
     * You cannot set a value for the key 'user', as it is kept for the user data.
     * If you want to set a user, see the login method
     * 
     * @see Session::login()
     *
     * @param  string $key The key of the data in the session
     * @param  mixed $value The value of the data
     * @return void
     */
    public static function set($key, $value): void
    {
        if(!in_array($key, ['user']))
            $_SESSION[$key] = $value;
    }
    
    /**
     * Returns the currently logged user object (that implements UserInterface)
     *
     * @see UserInterface
     * 
     * @return UserInterface|null Returns the logged user if someone is logged in, null otherwise
     */
    public static function getLoggedUser(): ?UserInterface
    {
        if(!isset($_SESSION['user']))
            return null;

        $userClass = self::getUserClass();

        if(!$userClass)
            return null;

        $user = new $userClass($_SESSION['user']);

        if(!$user->getId()) {
            self::logout();
            return null;
        }

        return $user;
    }
    
    /**
     * Verifies that a user with the given identifier and password exists, and stores its ID in the session
     *
     * @param  string $identifier The unique identifier of the user
     * @param  string $password The password in plaintext of the user
     * @return bool Returns true if the credentials match and the user is successfully logged in, else otherwise
     */
    public static function login($identifier, $password): bool
    {
        if(isset($_SESSION['user']))
            self::logout();

        $userClass = self::getUserClass();

        if(!$userClass)
            return false;

        $user = $userClass::getBy([
            $userClass::getIdentifierAttributeName() => $identifier 
        ]);

        if(count($user) == 0)
            return false;

        $user = $user[0];

        if($user->checkPassword($password)) {
            $_SESSION['user'] = $user->getId();
            return true;
        }
        else {
            return false;
        }
    }
    
    /**
     * Logs out the user
     *
     * @return void
     */
    public static function logout(): void
    {
        unset($_SESSION['user']);
    }
    
    /**
     * Returns the name of the class used to represent users. This class must implements UserInterface
     * 
     * @see UserInterface
     *
     * @return string The name of the class used to represent users.
     */
    public static function getUserClass(): ?string
    {
        if(self::$userClass != '')
            return self::$userClass;

        
        foreach(glob('Entity/*.php') as $filename) {
            preg_match('/\/(\w+)\.php/', $filename, $matches);
            $class = 'App\\Entity\\'.$matches[1];

            if(is_a($class, UserInterface::class, true)) {
                self::$userClass = $class;
                return self::$userClass;
            }
        }

        return null;
    }
}