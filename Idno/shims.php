<?php

    if (!interface_exists('SessionHandlerInterface')) {
        interface SessionHandlerInterface
        {
            public function close();

            public function destroy($session_id);

            public function gc($maxlifetime);

            public function open($save_path, $name);

            public function read($session_id);

            public function write($session_id, $session_data);
        }
    }

    if (!function_exists('xmlrpc_decode')) {

        function xmlrpc_decode($this_will_be_ignored)
        {
            return false;
        }

    }