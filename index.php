<?php 
/**
* Автор: Дмитрий Пешко
*
* Дата реализации: 04.11.2022 15:09
*
* Дата изменения: 06.11.2022 13:00
*
* Утилита для работы с базой данных 
*/
require 'Person.php';
require 'PersonList.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=slmax', 'root', 'root');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

} catch (PDOException $e) {
    print 'Произошла ошибка при подклбючении к БД.' . $e->getMessage();
}
/**
 * Создание таблицы для работы утилиты
 */
// $newTeble = $db->exec("CREATE TABLE `person` (
//                         `id` INT, `name` VARCHAR(50),
//                         `last_name` VARCHAR(50),
//                         `date_of_birth` DATE,
//                         `gender` INT,
//                         `sity_of_birth` TEXT
//                         )");








?>
