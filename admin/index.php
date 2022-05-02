<?

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Project\Stations;
use Project\Departures;

$station = new Stations();
$departures = new Departures();

//Данные из формы
$stationName = $_POST['name_station'] ?? false;

//Проверка на пустоту поля
if ($stationName !== false) {
    if (trim($stationName) === '') {
        $resStationAdd = 'Название не заполнено';
    } else {
        $resStationAdd = $station->setStation($stationName);
    }
}

//Данные из формы
$stationId = $_POST['select_station'] ?? false;
$departureDate = $_POST['date_departure'] ?? false;
$departureTime = $_POST['time_departure'] ?? false;

//Проверка на пустоту полей
if ($stationId || $departureDate || $departureTime) {
    try {
        if (!$stationId) {
            throw new \Exception('Станция не выбрана');
        }
        if (!$departureDate) {
            throw new \Exception('Дата не выбрана');
        }
        if (!$departureTime) {
            throw new \Exception('Время не выбрано');
        }
        $resDepartureAdd = $departures->setDeparture($stationId, $departureDate, $departureTime);
    } catch (\Exception $e) {
        $resDepartureAdd = $e->getMessage();
    }
}

//Получение списка станций
$stationList = $station->getList();

require_once $_SERVER['DOCUMENT_ROOT'] . '/header.php'; ?>
    <main class="container">
        <div class="row">
            <form class="col-6" action="" method="post">
                <div class="form-group mt-3">
                    <label for="name_station">Название станции</label>
                    <input class="form-control" aria-describedby="name_station_mes" type="text" name="name_station"
                           id="name_station">
                    <? if (isset($resStationAdd)) { ?>
                        <small id="name_station_mes" class="form-text text-muted"><?= $resStationAdd; ?></small>
                    <? } ?>
                </div>
                <input class="btn btn-primary mt-3" type="submit" value="Добавить станцию">
            </form>
            <form class="col-6" action="" method="post">
                <div class="form-group mt-3">
                    <label for="name_station">Выберите отправление</label>
                    <select class="form-select mb-3" name="select_station" aria-label="Не выбрано">
                        <? foreach ($stationList as $key => $station) { ?>
                            <option <?= $key === 0 ? 'selected' : ''; ?> value="<?= $station['id']; ?>">
                                <?= $station['name']; ?>
                            </option>
                        <? } ?>
                    </select>
                    <div class="row">
                        <div class="container">
                            <input value="<?= $departureDate ?? ''; ?>" name="date_departure" class="col-6 px-2"
                                   type="date">
                            <input value="<?= $departureTime ?? ''; ?>" name="time_departure" class="col-4 px-2"
                                   type="time">
                        </div>
                    </div
                    <? if (isset($resDepartureAdd)) { ?>
                        <small id="name_station_mes" class="form-text text-muted"><?= $resDepartureAdd; ?></small>
                    <? } ?>
                </div>
                <input class="btn btn-primary mt-3" type="submit" value="Добавить отправление">
            </form>
        </div>
    </main>
<? require_once $_SERVER['DOCUMENT_ROOT'] . '/footer.php'; ?>