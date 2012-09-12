<?php
    include_once(dirname(__FILE__) . '/EventSource.php');

    $events = new EventSource(array(
        'delay' => 1,
        'origin' => '*'
    ));

    $events->on('check', function() {
        //this method will check for new events from Mysql, MemcacheQ, ...
        //if events found we return array($event1, $event2, ...);
        //else return false or nothing
        return 'test';
    });
?>
