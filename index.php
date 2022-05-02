<?

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Project\Stations;
use Project\Departures;

$stations = new Stations();
$departures = new Departures();

$dateNow = date('Y-m-d');
$dateTimeNow = date('Y-m-d H:i');

//Получаем данные формы
$stationId = $_POST['select_station'] ?? false;
$departureDate = $_POST['date_departure'] ?? false;

//Если дата сегодняшняя, то добавляем время
if ($departureDate === $dateNow) {
    $departureDate = $dateTimeNow;
}
//Получаем список станций для отображения
$stationList = $stations->getList();

//Приводим к формату для отображения
if ($departureDate) {
    $departureDateFormat = date('d.m.Y', strtotime($departureDate));
}

//Проверка на заполненность
if ($stationId || $departureDate) {
    try {
        if (!$stationId) {
            throw new \Exception('Станция не выбрана');
        }
        if (!$departureDate) {
            throw new \Exception('Дата не выбрана');
        }
        $arDepartures = $departures->getList($departureDate, $stationId);

    } catch (\Exception $e) {
        //Если не заполнены поля, выводим сегодняшний отправления
        $arDepartures = $departures->getList($dateTimeNow);
        $resDepartureSearch = $e->getMessage();
    }
} else {
    //Если отсутствуют данные формы, выводим сегодняшний отправления
    $arDepartures = $departures->getList($dateTimeNow);
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/header.php';
?>
<main>
    <div class="container">
        <div class="row">
            <div class="col-6">
                <h4>Отправления на <?= $departureDateFormat ?? 'сегодня' ?></h4>
                <? if (isset($arDepartures['error'])) { ?>
                    <p><?= $arDepartures['error']; ?></p>
                <? } else { ?>
                    <ul>
                        <? foreach ($arDepartures as $departure) { ?>
                            <li><?= $departure['name'] . ' - ' . $departure['date'] ?></li>
                        <? } ?>
                    </ul>
                <? } ?>
            </div>
            <div class="col-6">
                <h4>Выберите дату и конечную станцию</h4>
                <form class="col-6" action="" method="post">
                    <div class="form-group mt-3">
                        <select class="form-select mb-3" name="select_station" aria-label="Не выбрано">
                            <? foreach ($stationList as $key => $station) { ?>
                                <option <?= $key === 0 ? 'selected' : ''; ?> value="<?= $station['id']; ?>">
                                    <?= $station['name']; ?>
                                </option>
                            <? } ?>
                        </select>
                        <input value="<?= $departureDate ?? ''; ?>" name="date_departure" class="w-100 px-2"
                               type="date">
                        <? if (isset($resDepartureSearch)) { ?>
                            <small id="name_station_mes"
                                   class="form-text text-muted"><?= $resDepartureSearch; ?></small>
                        <? } ?>
                    </div>
                    <input class="btn btn-primary mt-3" type="submit" value="Найти отправление">
                </form>
            </div>
        </div>
    </div>
</main>

<? require_once $_SERVER['DOCUMENT_ROOT'] . '/footer.php'; ?>
