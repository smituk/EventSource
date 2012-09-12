<?php
    class EventSource
    {
        private $callbacks = array();
        private $config = array();

        public function __construct($config = array())
        {
            $this->config = array_merge(array(
                'delay' => 1,
                'origin' => '*',
                'json' => true,
                'base64' => true
            ), $config);

            if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ||
                $_SERVER['HTTP_ACCEPT'] == 'text/xhr-event-stream')
            {
                $this->xhr = true;
            }
        }

        public function on($event, $callback)
        {
            $this->callbacks[$event] = $callback;
        }

        public function listen()
        {
            $this->_prepare($this->config['origin']);
            while(true)
            {
                if($events = call_user_func_array($this->callbacks['check'], array($this->xhr)))
                {
                    $this->_out($events);
                }
                sleep($this->config['delay']);
            }
        }
        
        private function _prepare($origin = "*")
        {
            header('HTTP/1.1 200 OK');
            header('Cache-Control: no-cache');
            if($this->xhr)
            {
                return;
            }
            header('Content-Type: text/event-stream');
            header('Access-Control-Allow-Origin: ' . $origin);
            
            if(function_exists('apache_setenv'))
            {
                apache_setenv('no-gzip', 1);
            }
            @ini_set('output_buffering', 'Off');
            @ini_set('zlib.output_compression', 0);
            @ini_set('implicit_flush', 1);
            $levels = ob_get_level();
            for($level = 0; $level < $levels; $level++)
            {
                ob_end_flush();
            }
            ob_implicit_flush(1);
        }

        private function _out($data = '')
        {
            if($this->config['json'])
            {
                $data = json_encode($data);
            }
            
            if($this->config['base64'])
            {
                $data = base64_encode($data);
            }

            echo "data: " . $data . "\n\n";

            if($this->xhr)
            {
                exit();
            }
        }
    }
?>
