<?php
// Подключение 
require_once 'backend/sdbh.php';
$dbh = new sdbh();

// Получение списка продуктов из БД
$products = $dbh->mselect_rows('a25_products', [], 0, 100, 'ID');

// Получение дополнительных услуг из БД
$services = unserialize($dbh->mselect_rows('a25_settings', ['set_key' => 'services'], 0, 1, 'ID')[0]['set_value']);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="style_form.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <style>
        .container{
            margin-top: 50px;
            border-radius: 15px;
            border: 3px solid #333;
        }
        .col-3{
            background-color: #FF9A00;
            border-radius: 0;
            border-top-left-radius: 13px;
            border-bottom-left-radius: 13px;
            display: flex;
            align-items: center;
            flex-flow: column;
            justify-content: center;
            font-size: 26px;
            font-weight: 900;
        }
        label:not([class="form-check-label"]) {
            font-size: 16px;
            font-weight: 600;
        }
        .form-check-input:checked{
            background-color: #FF9A00;
            border-color: #FF9A00;
        }
        .col-9{
            padding: 25px;
        }
        .btn-primary {
            color: #fff;
            background-color: #FF9A00;
            border-color: #FF9A00;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row row-body">
            <div class="col-3">
                <span style="text-align: center">Форма расчета</span>
                <i class="bi bi-activity"></i>
            </div>
            <div class="col-9">
                <form action="" id="calcForm">
                    <label class="form-label" for="product">Выберите продукт:</label>
                    <select class="form-select" name="product" id="product">
                        <!-- Заполнение выпадающего списка продуктами из базы данных -->
                        <?php foreach($products as $product): ?>
                            <option value="<?= $product['ID'] ?>"><?= $product['NAME'] ?> за <?= $product['PRICE'] ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label for="days" class="form-label">Количество дней:</label>
                    <input type="number" class="form-control" id="days" name="days" min="1" max="30" value="1">

                    <label class="form-label">Дополнительно:</label>
                    <!-- Заполнение чекбоксов дополнительными услугами из базы данных -->
                    <?php foreach($services as $service => $price): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= $price ?>" id="service<?= $service ?>" name="services[]">
                            <label class="form-check-label" for="service<?= $service ?>">
                                <?= $service ?> за <?= $price ?>
                            </label>
                        </div>
                    <?php endforeach; ?>

                    <button type="submit" class="btn btn-primary">Рассчитать</button>
                </form>
                <!-- Блок для отображения результата расчета -->
                <div id="result" class="mt-3"></div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Обработчик отправки формы
            $('#calcForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: 'backend/calculate.php', 
                    data: $(this).serialize(),
                    success: function(response) {
                        // Отображение результата расчета
                        $('#result').html('<h3>Итоговая стоимость: ' + response + ' руб.</h3>');
                    }
                });
            });
        });
    </script>
</body>
</html>
