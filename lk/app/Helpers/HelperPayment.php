<?php

namespace App\Helpers;

class HelperPayment extends Helper
{

    /**
     * Статусы для реквезитов
     */
    const RQ_STATUS_CREATE = 101;
    const RQ_STATUS_PARTIAL_PAYMENT = 102;
    const RQ_STATUS_CANCEL = 103;
    const RQ_STATUS_ERROR = 104;
    const RQ_STATUS_PAID = 105;

    /**
     * Статусы для карты
     */
    const CD_STATUS_CREATE = 201;
    const CD_STATUS_PAYMENT = 202;
    const CD_STATUS_CANCEL = 203;
    const CD_STATUS_ERROR = 204;
    const CD_STATUS_PAID = 205;
    const CD_STATUS_AUTHORIZE = 206;

    /**
     * Общие статусы
     */

    const STATUS_COMPLETE = 300;

    /**
     * Типы оплаты
     */
    const TYPE_RQ = 504;
    const TYPE_CD = 506;
    const TYPE_REN = 507; //Изменено администратором
    const TYPE_CT = 508; //Рассрчока

    /**
     * Статусы кредита
     */
    const CT_STATUS_CREATE = 401; //Создание Заявки
    const CT_STATUS_APPROVED = 402; //Заявка одобрена. Клиенту остается подписать документы по СМС или на встрече с представителем банка
    const CT_STATUS_REJECT = 403; //По заявке отказ. Вы можете связаться с клиентом и предложить альтернативные способы оплаты - кредитная карта, наличные
    const CT_STATUS_CANCELED = 404; //Заявка отменена. Клиент по какой-то причине отменил заказ
    const CT_STATUS_SIGNED = 405; //Договор подписан клиентом через СМС или на встрече с представителем банка. Вы можете выдать товар клиенту или оказать купленную услугу

    public static function number2string($number)
    {

        // обозначаем словарь в виде статической переменной функции, чтобы 
        // при повторном использовании функции его не определять заново
        static $dic = array(

            // словарь необходимых чисел
            array(
                -90 => 'девяносто',
                -80 => 'восемьдесят',
                -70 => 'семьдесят',
                -60 => 'шестьдесят',
                -50 => 'пятьдесят',
                -40 => 'сорок',
                -30 => 'тридцать',
                -20 => 'двадцать',
                -19    => 'девятнадцать',
                -18    => 'восемнадцать',
                -17    => 'семнадцать',
                -16    => 'шестнадцать',
                -15    => 'пятнадцать',
                -14    => 'четырнадцать',
                -13    => 'тринадцать',
                -12    => 'двенадцать',
                -11    => 'одиннадцать',
                -10 => 'десять',
                -2    => 'две',
                -1    => 'одна',
                1    => 'один',
                2    => 'два',
                3    => 'три',
                4    => 'четыре',
                5    => 'пять',
                6    => 'шесть',
                7    => 'семь',
                8    => 'восемь',
                9    => 'девять',
                10    => 'десять',
                11    => 'одиннадцать',
                12    => 'двенадцать',
                13    => 'тринадцать',
                14    => 'четырнадцать',
                15    => 'пятнадцать',
                16    => 'шестнадцать',
                17    => 'семнадцать',
                18    => 'восемнадцать',
                19    => 'девятнадцать',
                20    => 'двадцать',
                30    => 'тридцать',
                40    => 'сорок',
                50    => 'пятьдесят',
                60    => 'шестьдесят',
                70    => 'семьдесят',
                80    => 'восемьдесят',
                90    => 'девяносто',
                100    => 'сто',
                200    => 'двести',
                300    => 'триста',
                400    => 'четыреста',
                500    => 'пятьсот',
                600    => 'шестьсот',
                700    => 'семьсот',
                800    => 'восемьсот',
                900    => 'девятьсот'
            ),

            // словарь порядков со склонениями для плюрализации
            array(
                array('рубль', 'рубля', 'рублей'),
                array('тысяча', 'тысячи', 'тысяч'),
                array('миллион', 'миллиона', 'миллионов'),
                array('миллиард', 'миллиарда', 'миллиардов'),
                array('триллион', 'триллиона', 'триллионов'),
                array('квадриллион', 'квадриллиона', 'квадриллионов'),
                // квинтиллион, секстиллион и т.д.
            ),

            // карта плюрализации
            array(
                2, 0, 1, 1, 1, 2
            )
        );

        // обозначаем переменную в которую будем писать сгенерированный текст
        $string = array();

        // дополняем число нулями слева до количества цифр кратного трем,
        // например 1234, преобразуется в 001234
        $number = str_pad($number, ceil(strlen($number) / 3) * 3, 0, STR_PAD_LEFT);

        // разбиваем число на части из 3 цифр (порядки) и инвертируем порядок частей,
        // т.к. мы не знаем максимальный порядок числа и будем бежать снизу
        // единицы, тысячи, миллионы и т.д.
        $parts = array_reverse(str_split($number, 3));

        // бежим по каждой части
        foreach ($parts as $i => $part) {

            // если часть не равна нулю, нам надо преобразовать ее в текст
            if ($part > 0) {

                // обозначаем переменную в которую будем писать составные числа для текущей части
                $digits = array();

                // если число треххзначное, запоминаем количество сотен
                if ($part > 99) {
                    $digits[] = floor($part / 100) * 100;
                }

                // если последние 2 цифры не равны нулю, продолжаем искать составные числа
                // (данный блок прокомментирую при необходимости)
                if ($mod1 = $part % 100) {
                    $mod2 = $part % 10;
                    $flag = $i == 1 && $mod1 != 11 && $mod1 != 12 && $mod2 < 3 ? -1 : 1;
                    if ($mod1 < 20 || !$mod2) {
                        $digits[] = $flag * $mod1;
                    } else {
                        $digits[] = floor($mod1 / 10) * 10;
                        $digits[] = $flag * $mod2;
                    }
                }

                // берем последнее составное число, для плюрализации
                $last = abs(end($digits));

                // преобразуем все составные числа в слова
                foreach ($digits as $j => $digit) {
                    $digits[$j] = $dic[0][$digit];
                }

                // добавляем обозначение порядка или валюту
                $digits[] = $dic[1][$i][(($last %= 100) > 4 && $last < 20) ? 2 : $dic[2][min($last % 10, 5)]];

                // объединяем составные числа в единый текст и добавляем в переменную, которую вернет функция
                array_unshift($string, join(' ', $digits));
            }
        }

        // преобразуем переменную в текст и возвращаем из функции, ура!
        return join(' ', $string);
    }
}
