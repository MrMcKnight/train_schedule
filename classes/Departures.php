<?php

namespace Project;

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

class Departures
{
    public function setDeparture($stationId, $date, $time): string
    {
        try {
            $db = new DB();
            $db = $db->getConnect();

            if (mysqli_connect_errno()) {
                throw new \Exception('Не удалось подключиться к базе данных');
            }

            //Соединяем полученные дату и время
            $dateTime = $date . ' ' . $time;

            //Приводим к html-безопасному виду
            $stationId = htmlspecialchars($stationId);
            $dateTime = htmlspecialchars($dateTime);

            //Проверяем наличие такого отправления
            $departureIsset = $db->query(
                "SELECT `departure_id` FROM departures WHERE station_id = '$stationId' AND `date` = '$dateTime'"
            )->fetch_array();

            if ($departureIsset) {
                throw new \Exception('Такое отправление уже существует');
            }

            $db->query("INSERT INTO departures SET station_id = '$stationId', `date` = '$dateTime'");

            if (mysqli_error($db)) {
                throw new \Exception('Ошибка добавления записи');
            }

            $return = 'Отправление успешно добавлено';
        } catch (\Exception $e) {
            $return = $e->getMessage();
        }

        return $return;
    }

    public function getList($dateTime, $stationId = ''): array
    {
        try {
            $db = new DB();
            $db = $db->getConnect();

            if (mysqli_connect_errno()) {
                throw new \Exception('Не удалось подключиться к базе данных');
            }

            $dateTime = htmlspecialchars($dateTime);

            //Получаем дату из полной даты
            $date = date('Y-m-d', strtotime($dateTime));

            if ($stationId) {
                $departures = $db->query(
                    "SELECT DATE_FORMAT(`date`, '%d.%m.%Y %H:%i') as `date`, `name`, `station_id`
                        FROM departures LEFT JOIN stations ON `id` = station_id
                        WHERE `date` > '$dateTime' AND DATE(`date`) = '$date' AND `station_id` = '$stationId' ORDER BY `date` ASC"
                )->fetch_all(MYSQLI_ASSOC);
            } else {
                $departures = $db->query(
                    "SELECT DATE_FORMAT(`date`, '%d.%m.%Y %H:%i') as `date`, `name`
                        FROM departures LEFT JOIN stations ON `id` = station_id
                        WHERE `date` > '$dateTime' AND DATE(`date`) = '$date' ORDER BY `date` ASC"
                )->fetch_all(MYSQLI_ASSOC);
            }

            if (mysqli_error($db)) {
                throw new \Exception('Ошибка получения расписания');
            }

            if (!$departures) {
                throw new \Exception('Отправлений не найдено');
            }

            $return = $departures;
        } catch (\Exception $e) {
            $return['error'] = $e->getMessage();
        }

        return $return;
    }
}