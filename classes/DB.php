<?php
namespace Project;

class DB
{
    public function getConnect()
    {
        $connect = mysqli_init();
        $connect->real_connect( 'localhost', 'admin', 'admin', 'test_task');
        return $connect;
    }
}