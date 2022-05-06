<?php
/**
 * UserInterface interface
 */

namespace App\Session;

/**
 * This interface serves as an identifier of a class that represents a user in the application.
 * Users must have a unique identifier and a password, as well as a method that can check that a given password corresponds to the user's one
 * This class should be implemented by only one class. This class should also inherit from Entity
 * 
 * @see Entity
 */
interface UserInterface {
    
    /**
     * Returns the identifier value of the user (should be unique)
     *
     * @return string The identifier value of the user
     */
    public function getIdentifier(): string;
    
    /**
     * Returns the name of the identifier attribute in the user's class
     *
     * @return string The name of the identifier attribute
     */
    public static function getIdentifierAttributeName(): string;
    
    /**
     * Returns the usser's password (should be crypted)
     *
     * @return string The usser's password
     */
    public function getPassword(): string;
    
    /**
     * Checks if a given password in plaintext matches with the user's crypted password.
     *
     * @param  mixed $password A password in plaintext
     * @return bool Returns true if the password matches, false if it doesn't
     */
    public function checkPassword($password): bool;

}