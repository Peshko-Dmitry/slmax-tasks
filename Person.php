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
* class Person
* Класс позволяет добавлять, удалять пользователей в БД.
* Преобразовать возраст человека (полных лет) в зависимости от даты рождения.
* Преобразовать пол из двоичной системы в текстовую (муж, жен).
* Возвращать новый экземпляр StdClass со всеми полями изначального класса с учетом 
* преобразованного возраста и пола.
*/

class Person
{

    public $id;
    public $name;
    public $lastName;
    public static $dateOfBirth;
    public static $gender;
    public $sityOfBirth;

/**
 * Конструктор класса создает нового человека если человека
 * с таким id нет в БД. Если человек с таким id есть в БД берет данные из БД.
 * Если такого человека нет то производит валидацию данных и присваивает их 
 * значения свойствам класса
 */

    public function __construct($id, $name, $lastName, $dateOfBirth, $gender, $sityOfBirth)
    {
        global $db;

        $sql = "SELECT `id`, `name`, `last_name`, `date_of_birth`, `gender`, `sity_of_birth` 
                FROM `person` WHERE id=?";
        $stmt = $db->prepare($sql);
        $stmt->execute(array($id));
        $person = $stmt->fetch();

        if (isset($person->id)) {
            $this->id = $person->id;
            $this->name = $person->name;
            $this->lastName = $person->last_name;
            self::$dateOfBirth = $person->date_of_birth;
            self::$gender = $person->gender;
            $this->sityOfBirth = $person->sity_of_birth;
            
        } else {

            if (is_int($id)) {
                $this->id = $id;
            } else {
                print 'Id должен быть целым числом!';
            }
            
            $patternName = '/^[а-яА-ЯёЁa-zA-Z]+$/iu';
            $patternLastName = '/^[а-яА-ЯёЁa-zA-Z -]+$/iu';
            $patternDate ='/[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])/';

            if (preg_match($patternName, $name)) {
                $this->name = $name;
            } else {
                print 'Имя должно состоять только из букв!'; 
            }

            if (preg_match($patternLastName, $lastName)) {
                $this->lastName = $lastName;
            } else {
                print 'Фамилия должна состоять только из букв, пробела или знака дефиc!'; 
            }

            if (preg_match($patternDate, $dateOfBirth)) {
                self::$dateOfBirth = $dateOfBirth;
            } else {
                print 'Дата должна быть в формате YYYY-MM-DD!'; 
            }

            if ($gender === 0 || $gender === 1) {
                self::$gender = $gender;
            } else {
                print 'Полл должен целым числом 0 или 1'; 
            }

            if (is_string($sityOfBirth)) {
                $this->sityOfBirth = $sityOfBirth;
            } else {
                print 'Город рождения должен быть строкой!'; 
            }
        }
    }

    /**
     * метод checkIdInBd проверяет есть ли пользователь с таким id в БД.
     * если есть возвращает true.
     */

    public function checkIdInBd()
    {
        global $db;

        $sql = "SELECT `id` FROM `person` WHERE id=?";
        $stmt = $db->prepare($sql);
        $stmt->execute(array($this->id));
        $person = $stmt->fetch();

        return !empty($person);
    }

    /**
     * метод save сохраняет поля экземпляра класса в БД, если такого человека нет в БД
     */

    public function save()
    {
        global $db;

        if ($this->checkIdInBd()) {
            print 'Человек с таким id уже существует!';
        } else {
            $newPerson = array(
                        $this->id, $this->name, $this->lastName, 
                        self::$dateOfBirth, self::$gender, $this->sityOfBirth
            );

            $sql = "INSERT INTO `person`(`id`, `name`, `last_name`, `date_of_birth`, 
                                        `gender`, `sity_of_birth`) VALUES (?,?,?,?,?,?)";
            $stmt = $db->prepare($sql);
            $stmt->execute($newPerson);
        }
    }

    /**
     * метод delete удаляет человека из БД, если такой человека есть в БД
     */

    public function delete()
    {
        global $db;

        if ($this->checkIdInBd()) {
            $sql = "DELETE FROM `person` WHERE id=?";
            $stmt = $db->prepare($sql);
            $stmt->execute(array($this->id));
        } else {
            print 'Человек с таким id не существует в БД!';
        }
        
    }

    /**
     * метод dateConversion преобразует дату рождения в возраст человека
     */

     static public function dateConversion()
    {
        $dateBirth = explode('-', self::$dateOfBirth);
        $newDate = explode('-', date("Y-m-d"));

        $age = $newDate[0] - $dateBirth[0] ;

        if ($dateBirth[1] > $newDate[1]) {
            $age--;
        }

        if ($dateBirth[1] === $newDate[1]) {

            if ($dateBirth[2] > $newDate[2]) {
                $age--;
            }
        }
       
        return $age;
    }

    /**
     * метод genderConversion преобразует пол из двоичной системы в текстовую (муж, жен)
     */

    public static function genderConversion()
    {
        $personGender = self::$gender;

        if ($personGender === 0) {
            return 'mуж';
        } else {
            return 'жен';
        }
    }

    /**
     * метод personFormatting форматирует человека с преобразованием возраста и пола.
     * возвращает новый экземпляр StdClass со всеми полями изначального класса
     */

    public function personFormatting() 
    {
        $formattinPersonData = new stdClass();
        $formattinPersonData->id = $this->id;
        $formattinPersonData->name = $this->name;
        $formattinPersonData->lastName = $this->lastName;
        $formattinPersonData->dateOfBirth = self::dateConversion() ;
        $formattinPersonData->gender = self::genderConversion();
        $formattinPersonData->sityOfBirth = $this->sityOfBirth;

        return $formattinPersonData;
    }
}
?>
