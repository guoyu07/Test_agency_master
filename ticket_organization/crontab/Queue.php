<?php
require dirname(__FILE__) . '/Base.php';

class Crontab_Queue extends Process_Base 
{
    public function run() {
        $this->reg();
        Util_Queue::receiveAll();
    }

    public function reg() {
        // User
        Util_Queue::reg("log", array('Log_Import', 'run'));
    }
}

$test = new Crontab_Queue;
