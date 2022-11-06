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

/**
 * Проверяем был ли обьявлен класс Person
 */
try {
    if (!class_exists('Person')) {
        throw new Exception('Класс Person не был обьявлен!');
    }
} catch (Exception $e) {
    print $e->getMessage();
    die();
}

/**
* Класс PersonList
* Класс может осуществлять поиск людей по id с использованием операторов 
* сравнения (<, >, !=) в БД.
* Записывать id всех найденых людей в массив.
* Получать массив экземпляров класса Person из массива с id людей 
* полученного в конструкторе
* Удалять людей из БД с помощью экземпляров класса Person в 
*  соответствии с массивом, полученным в конструкторе
*/

class PersonList
{
    public $list;

    /**
    * Конструктор осуществляет поиск  людей по id, используя опператоры 
    * сравнения (<, >, !=) 
    * Осуществляет валидацию входящих данных (id и оператора сравнения)
    * Oтправляет запрос в БД и получает все id в соответствии с условием,
    * после чего записывает все id в массив $list
    */

    public function __construct($id, $operator)
    {
        global $db;

        $operators = array('<', '>', '!=');

        if (is_int($id)) {

            if (in_array($operator, $operators)) {
                $sql = "SELECT `id` FROM `person` WHERE id $operator ?";
                $stmt = $db->prepare($sql);
                $stmt->execute(array($id));

                while ($row=$stmt->fetch()) {
                    $this->list[] = $row->id;
                }
            } else {
                print 'Поддерживаются только выражения( >, <, != )';
            }

        } else {
            print 'Id должен быть целым числом!';
        }
        
    }
    /**
    * Метод getArray получает массив экземпляров класса Person из массива $list
    * полученного в конструкторе.
    */

    public function getArray() 
    {
        $arrayPerson = array();

        if (!empty($this->list)) {
            for ($i = 0; $i < count($this->list); $i++) {
                $person = new Person($this->list[$i], null, null, null, 0, null);
                $arrayPerson[] = $person;
            }
        } else {
            print 'Список id пуст!';
        }
        
        return $arrayPerson;
    }

    /**
     * Метод delete удаляет людей из БД используя экземпляры класса Person 
     * в соответствии со списком id в массиве $list
     */

    public function delete() 
    {
        if (!empty($this->list)) {
            for ($i = 0; $i < count($this->list); $i++) {
                $person = new Person($this->list[$i], null, null, null, 0, null);
                $person->delete();
            }
        } else {
            print 'Список id пуст!';
        }
    }
} 


?>