<?php
/**
 * Mail class
 */

namespace App;

/**
 * This class allows to send mails
 */
class Mail {
        
    /**
     * Sends a mail
     *
     * @param  string $to The email address that will receive the mail
     * @param  string $subject The mail subject
     * @param  string $message The message to send
     * @param  string|array $headers The mail headers
     * @return bool Returns true if the mail was sent, false otherwise
     */
    public static function send($to, $subject, $message, $headers = ''): bool 
    {
        
        $tempheaders = $headers;
        if(!is_array($tempheaders)) {
            $tempheaders = str_replace("\r\n", "\n", $tempheaders);
            $tempheaders = explode("\n", $tempheaders);
        }

        $headers = [];

        foreach($tempheaders as $key => $header) {
            if(strpos($header, ':') === false) {
                $name = $key;
                $value = $header;
            }
            else {
                list($name, $value) = explode(':', trim($header), 2);
            }

            $name = trim($name);
            $value = trim($value);

            switch(strtolower($name)) {
                case 'from':
                    $bracket_pos = strpos($value, '<');

                    if($bracket_pos !== false) {
                        if($bracket_pos > 0) {
                            $from_name = substr($value, 0, $bracket_pos-1);
                            $from_name = trim(str_replace('"', '', $from_name));
                        }
                        $from_email = substr($value, $bracket_pos+1);
                        $from_email = trim(str_replace('>', '', $from_email));
                    }
                    else if($value != '') {
                        $from_email = $value;
                    }
                    break;

                default:
                    $header[$name] = $value;
                    break;
            }

        }

        if(!isset($from_name))
            $from_name = SITENAME;

        if(!isset($from_email))
            $from_email = EMAIL;

        $headers['from'] = $from_name.' <'.$from_email.'>';

        if(!isset($headers['content-type']))
            $headers['content-type'] = 'text/html; charset=utf-8';

        return mail($to, $subject, $message, $headers);

    }
}