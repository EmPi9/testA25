<?php
require_once '../backend/sdbh.php';
$dbh = new sdbh();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = intval($_POST['product']);
    $days = intval($_POST['days']);
    $services = isset($_POST['services']) ? $_POST['services'] : [];

    // Получение информации о выбранном продукте из базы данных
    $product = $dbh->mselect_rows('a25_products', ['ID' => $productId], 0, 1, 'ID')[0];

    // Расчет базовой стоимости продукта с учетом дней
    $price = $product['PRICE'];
    $tariff = unserialize($product['TARIFF']);

    if ($tariff) {
        // Проверка наличия тарифов и их применение
        foreach ($tariff as $day => $tariffPrice) {
            if ($days >= $day) {
                $price = $tariffPrice;
            }
        }
    }

    // Итоговая стоимость за выбранное количество дней
    $totalCost = $price * $days;

    // Добавление стоимости дополнительных услуг
    foreach ($services as $servicePrice) {
        $totalCost += intval($servicePrice) * $days;
    }

    // Вывод итоговой стоимости
    echo $totalCost;
}
?>
