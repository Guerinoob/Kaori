<?php
/**
 * SocketServer class
 */

namespace App\WebSocket;

/**
 * This class represents a WebSocket server. It handles client connections and checks for received messages
 * A server instance must register a handler that will implement SocketHandlerInterface. This handler intercepts every event with the client
 * The server uses streams to handle the connections. These streams will be refered as sockets.
 * 
 * @see SocketHandlerInterface
 */
class SocketServer {
    
    /**
     * The socket of the server
     *
     * @var mixed
     */
    protected $master;
    
    /**
     * The hostname of socket server
     *
     * @var string
     */
    protected $url;
        
    /**
     * The port of socket server
     *
     * @var string
     */
    protected $port;
    
    /**
     * An array of connected clients
     *
     * @var array
     */
    protected $clients;
    
    /**
     * An array of every socket involved (the master + the clients)
     *
     * @var array
     */
    protected $sockets;
    
    /**
     * The maximum number of bytes that can be read for a message
     *
     * @var int
     */
    protected $maxBufferSize;
    
    /**
     * A handler that will react to various events (connection, data received, error, connection closed)
     *
     * @var SocketHandlerInterface
     */
    protected $handler;
    
    /**
     * Constructor - Initializes properties
     *
     * @param  string $url The hostname of socket server
     * @param  int $port The port of socket server
     * @param  int $bufferSize The maximum number of bytes that can be read for a message
     * @return void
     */
    public function __construct($url = '0.0.0.0', $port = SOCKET_PORT, $bufferSize = 2048)
    {
        $this->clients = [];
        $this->sockets = [];
        $this->url = $url;
        $this->port = $port;
        $this->maxBufferSize = $bufferSize;
    }
    
    /**
     * Registers a handler to the server
     *
     * @param  SocketHandlerInterface $handler An object that implements SocketHandlerInterface
     * @return void
     */
    public function registerHandler($handler): void
    {
        $this->handler = $handler;
        $handler->registerServer($this);
    }
    
    /**
     * Opens the socket server
     *
     * @return bool Returns true if the server is open, false otherwise
     */
    public function open(): bool
    {      
        $socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));

        if(!$socket)
            return false;

        socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);

        if(!socket_bind($socket, $this->url, $this->port) || !socket_listen($socket))
            return false;

        socket_set_nonblock($socket);
        
        $this->master = $socket;
        $this->sockets['master'] = ['socket' => $socket, 'id' => 'master'];
        
        return true;
    }
    
    /**
     * Runs the server
     *
     * @return void
     */
    public function run(): void
    {
        if(!$this->handler)
            return;

        $is_open = $this->open();
        
        if($is_open) {
        
            while(true) {
                foreach($this->sockets as $socket) {
                    if($socket['socket'] == $this->master) {
                        $this->accept();
                        continue;
                    }

                    $clientId = $socket['id'];
        
                    $client = $this->clients[$clientId];
        
                    $buffer = $this->receive($clientId);
        
                    if($buffer) {
                        if(!$client['handshake']) {
                            if(!$this->doHandshake($clientId, $buffer)) {
                                $this->handler->onError($clientId, 'Client could not do Handshake - closing connection');
                                $this->close($clientId);
                            }
                        }
                        else {
                            $this->handler->onData($clientId, $buffer);
                        }
                    }
                }
                
            }
        }
    }
    
    /**
     * Checks if a client is connecting to the server and if  that's the case, connects it to the server
     *
     * @return string|bool Returns false if no client has been connected, or a string identifier if the client is connected
     */
    public function accept()
    {
        $socket = @socket_accept($this->master);

        if(!$socket)
            return false;

        socket_set_nonblock($socket);

        $id = bin2hex(random_bytes(12));

        $this->clients[$id] = ['socket' => $socket, 'id' => $id, 'handshake' => false];
        $this->sockets[$id] = ['socket' => $socket, 'id' => $id];

        $this->handler->onOpen($id);

        return $id;
    }
    
    /**
     * Tries to read a message from the given client.
     * If the handshake has been done, the data will be decoded 
     *
     * @param  string $id The identifier of the client
     * @return string|bool Returns false if no message has been received, returns the decoded message otherwise
     */
    public function receive($id)
    {
        if(!isset($this->clients[$id]))
            return false;

        $s = $this->clients[$id]['socket'];

        $message = '';
        $buffer = '';

        while(@socket_recv($s, $buffer, $this->maxBufferSize, 0))
            $message .= $buffer;

        if($message == '')
            return false;

        if($this->clients[$id]['handshake'])
            $message = $this->unseal($message);

        return $message;
    }
    
    /**
     * Sends data to the given client
     * If the handshake has been done, the data will be encoded before it's sent
     *
     * @param  string $id The identifier of the client
     * @param  string $message The data to send
     * @return bool Returns true if the message has been sent, false otherwise
     */
    public function send($id, $message): bool
    {
        if(!isset($this->clients[$id]))
            return false;

        if($this->clients[$id]['handshake'])
            $message = $this->seal($message);

        return @socket_send($this->clients[$id]['socket'], $message, strlen($message), 0);
    }
    
    /**
     * Sends data to every connected client
     *
     * @param  string $message The data to send
     * @return void
     */
    public function sendAll($message): void
    {
        foreach($this->clients as $client) {
            $clientId = $client['id'];

            if(!$this->send($clientId, $message))
                $this->handler->onError($clientId, 'Message could not be sent');
        }
        
    }
    
    /**
     * Closes the connection with the given client, and removes it from the sockets and clients lists.
     *
     * @param  string $id The identifier of the client
     * @return void
     */
    public function close($id): void
    {
        if(isset($this->clients[$id]))
            fclose($this->clients[$id]['socket']);

        unset($this->clients[$id]);
        unset($this->sockets[$id]);

        $this->handler->onClose($id);
    }
    
    /**
     * Sends a handshake request to the client, based on the headers sent by the client
     * This has to be done after the connection. The client and the server can not communicate before it has been done
     *
     * @param  string $id The identifier of the client
     * @param  string $request The request headers sent by the client
     * @return bool Returns true if the handshake request has been sent, false otherwise
     */
    public function doHandshake($id,  $request): bool
    {

        $headers = $this->getHeaders($request);

        if(!isset($headers['Sec-WebSocket-Key']))
            return false;

        $acceptkey = "";
        $sah1 = sha1($headers['Sec-WebSocket-Key'] . "258EAFA5-E914-47DA-95CA-C5AB0DC85B11");
        for ($i = 0; $i < 20; $i++) {
            $acceptkey .= chr(hexdec(substr($sah1, $i * 2, 2)));
        }
        $acceptkey = base64_encode($acceptkey);

        $buffer  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
		"Upgrade: websocket\r\n" .
		"Connection: Upgrade\r\n" .
		"WebSocket-Origin: $this->url\r\n" .
		"WebSocket-Location: ws://$this->url:$this->port\r\n".
		"Sec-WebSocket-Accept:$acceptkey\r\n\r\n";
      
        if(socket_send($this->clients[$id]['socket'], $buffer, strlen($buffer), 0)) {
            $this->clients[$id]['handshake'] = true;
            return true;
        }

        return false;
    }
          
    /**
     * Returns an associative array corresponding to the headers given in parameters
     *
     * @param  string $request The request headers
     * @return array An associative array corresponding to the headers given in parameters
     */
    protected function getHeaders($data): array
    {
        $headers = array();
		$lines = preg_split("/\r\n/", $data);
		foreach($lines as $line)
		{
			$line = chop($line);
			if(preg_match('/\A(\S+): (.*)\z/', $line, $matches))
			{
				$headers[$matches[1]] = $matches[2];
			}
		}

        return $headers;
    }
    
    /**
     * Decodes a message sent by a client
     *
     * @param  string $message The message sent by the client
     * @return string The decoded message
     */
    protected function unseal($message): string
    {
		$length = ord($message[1]) & 127;
		if($length == 126) {
			$masks = substr($message, 4, 4);
			$data = substr($message, 8);
		}
		elseif($length == 127) {
			$masks = substr($message, 10, 4);
			$data = substr($message, 14);
		}
		else {
			$masks = substr($message, 2, 4);
			$data = substr($message, 6);
		}
		$message = "";
		for ($i = 0; $i < strlen($data); ++$i) {
			$message .= $data[$i] ^ $masks[$i%4];
		}
		return $message;
	}

    /**
     * Encodes a message to be send by the server
     *
     * @param  string $message The message to be send by the server
     * @return string The encoded message
     */
	protected function seal($message): string
    {
		$b1 = 0x80 | (0x1 & 0x0f);
		$length = strlen($message);
		
		if($length <= 125)
			$header = pack('CC', $b1, $length);
		elseif($length > 125 && $length < 65536)
			$header = pack('CCn', $b1, 126, $length);
		elseif($length >= 65536)
			$header = pack('CCNN', $b1, 127, $length);
		return $header.$message;
	}
}