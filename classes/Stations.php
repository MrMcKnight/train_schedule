<?php

namespace Project;

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

class Stations
{
    public function setStation($station): string
    {
        try {
            $db = new DB();
            $db = $db->getConnect();

            if (mysqli_connect_errno()) {
                throw new \Exception('Не удалось подключиться к базе данных');
            }

            //приводим к html-безопасному виду
            $station = htmlspecialchars($station);

            $stationIsset = $db->query("SELECT `name` FROM stations WHERE `name` = '$station'")->fetch_assoc();

            if ($stationIsset) {
                throw new \Exception('Станция уже существует');
            }

            $db->query("INSERT INTO stations SET `name`='$station'");

            $return = 'Станция успешно добавлена';
        } catch (\Exception $e) {
            $return = $e->getMessage();
        }
        return $return;
    }

    public function getList(): array
    {
        try {
            $db = new DB();
            $db = $db->getConnect();

            if (mysqli_connect_errno()) {
                throw new \Exception('Не удалось подключиться к базе данных');
            }

            $stationList = $db->query("SELECT `id`, `name` FROM stations")->fetch_all(MYSQLI_ASSOC);

            $return = $stationList;
        } catch (\Exception $e) {
            $return['error'] = $e->getMessage();
        }

        return $return;
    }
}