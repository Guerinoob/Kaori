<?php
/**
 * SocketHandlerInterface interface
 */

namespace App\WebSocket;

use App\WebSocket\SocketServer;

/**
 * This interface represents a handler that will handle events from a SocketServer, such as messages received, opened and closed connections, or errors.
 * 
 * @see SocketServer
 */
interface SocketHandlerInterface {    
    /**
     * Triggers when a message sent by a client is received
     *
     * @param  string $socketId The identifier of the client that sent the message
     * @param  string $message The decoded client's message
     * @return void
     */
    public function onData($socketId, $message): void;
    
    /**
     * Triggers when a client connection is opened
     *
     * @param  string $socketId The identifier of the client that connected
     * @return void
     */
    public function onOpen($socketId): void;
    
    /**
     * Triggers when an error occured for the client
     *
     * @param  string $socketId The identifier of the concerned client
     * @param  string $error The error message
     * @return void
     */
    public function onError($socketId, $error): void;
    
    /**
     * Triggers when a client connection is closed
     *
     * @param  string $socketId The identifier of the concerned client
     * @return void
     */
    public function onClose($socketId): void;
    
    /**
     * Registers a SocketServer. This method is called by the SocketServer when it registers the handler
     * 
     * @see SocketServer::registerHandler()
     *
     * @param  SocketServer $server The SocketServer isntance
     * @return void
     */
    public function registerServer(SocketServer $server): void;
}