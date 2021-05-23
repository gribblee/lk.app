<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RegionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('regions')->delete();

        \DB::table('regions')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'Адыгея',
                'type' => 'Респ',
                'name_with_type' => 'Респ Адыгея',
                'federal_district' => 'Южный',
                'kladr_id' => '0100000000000',
                'fias_id' => 'd8327a56-80de-4df2-815c-4f6ab1224c50',
            ),
            1 =>
            array(
                'id' => 2,
                'name' => 'Башкортостан',
                'type' => 'Респ',
                'name_with_type' => 'Респ Башкортостан',
                'federal_district' => 'Приволжский',
                'kladr_id' => '0200000000000',
                'fias_id' => '6f2cbfd8-692a-4ee4-9b16-067210bde3fc',
            ),
            2 =>
            array(
                'id' => 3,
                'name' => 'Бурятия',
                'type' => 'Респ',
                'name_with_type' => 'Респ Бурятия',
                'federal_district' => 'Дальневосточный',
                'kladr_id' => '0300000000000',
                'fias_id' => 'a84ebed3-153d-4ba9-8532-8bdf879e1f5a',
            ),
            3 =>
            array(
                'id' => 4,
                'name' => 'Алтай',
                'type' => 'Респ',
                'name_with_type' => 'Респ Алтай',
                'federal_district' => 'Сибирский',
                'kladr_id' => '0400000000000',
                'fias_id' => '5c48611f-5de6-4771-9695-7e36a4e7529d',
            ),
            4 =>
            array(
                'id' => 5,
                'name' => 'Дагестан',
                'type' => 'Респ',
                'name_with_type' => 'Респ Дагестан',
                'federal_district' => 'Северо-Кавказский',
                'kladr_id' => '0500000000000',
                'fias_id' => '0bb7fa19-736d-49cf-ad0e-9774c4dae09b',
            ),
            5 =>
            array(
                'id' => 6,
                'name' => 'Ингушетия',
                'type' => 'Респ',
                'name_with_type' => 'Респ Ингушетия',
                'federal_district' => 'Северо-Кавказский',
                'kladr_id' => '0600000000000',
                'fias_id' => 'b2d8cd20-cabc-4deb-afad-f3c4b4d55821',
            ),
            6 =>
            array(
                'id' => 7,
                'name' => 'Кабардино-Балкарская',
                'type' => 'Респ',
                'name_with_type' => 'Респ Кабардино-Балкарская',
                'federal_district' => 'Северо-Кавказский',
                'kladr_id' => '0700000000000',
                'fias_id' => '1781f74e-be4a-4697-9c6b-493057c94818',
            ),
            7 =>
            array(
                'id' => 8,
                'name' => 'Калмыкия',
                'type' => 'Респ',
                'name_with_type' => 'Респ Калмыкия',
                'federal_district' => 'Южный',
                'kladr_id' => '0800000000000',
                'fias_id' => '491cde9d-9d76-4591-ab46-ea93c079e686',
            ),
            8 =>
            array(
                'id' => 9,
                'name' => 'Карачаево-Черкесская',
                'type' => 'Респ',
                'name_with_type' => 'Респ Карачаево-Черкесская',
                'federal_district' => 'Северо-Кавказский',
                'kladr_id' => '0900000000000',
                'fias_id' => '61b95807-388a-4cb1-9bee-889f7cf811c8',
            ),
            9 =>
            array(
                'id' => 10,
                'name' => 'Карелия',
                'type' => 'Респ',
                'name_with_type' => 'Респ Карелия',
                'federal_district' => 'Северо-Западный',
                'kladr_id' => '1000000000000',
                'fias_id' => '248d8071-06e1-425e-a1cf-d1ff4c4a14a8',
            ),
            10 =>
            array(
                'id' => 11,
                'name' => 'Коми',
                'type' => 'Респ',
                'name_with_type' => 'Респ Коми',
                'federal_district' => 'Северо-Западный',
                'kladr_id' => '1100000000000',
                'fias_id' => 'c20180d9-ad9c-46d1-9eff-d60bc424592a',
            ),
            11 =>
            array(
                'id' => 12,
                'name' => 'Марий Эл',
                'type' => 'Респ',
                'name_with_type' => 'Респ Марий Эл',
                'federal_district' => 'Приволжский',
                'kladr_id' => '1200000000000',
                'fias_id' => 'de2cbfdf-9662-44a4-a4a4-8ad237ae4a3e',
            ),
            12 =>
            array(
                'id' => 13,
                'name' => 'Мордовия',
                'type' => 'Респ',
                'name_with_type' => 'Респ Мордовия',
                'federal_district' => 'Приволжский',
                'kladr_id' => '1300000000000',
                'fias_id' => '37a0c60a-9240-48b5-a87f-0d8c86cdb6e1',
            ),
            13 =>
            array(
                'id' => 14,
                'name' => 'Саха /Якутия/',
                'type' => 'Респ',
                'name_with_type' => 'Респ Саха /Якутия/',
                'federal_district' => 'Дальневосточный',
                'kladr_id' => '1400000000000',
                'fias_id' => 'c225d3db-1db6-4063-ace0-b3fe9ea3805f',
            ),
            14 =>
            array(
                'id' => 15,
                'name' => 'Северная Осетия - Алания',
                'type' => 'Респ',
                'name_with_type' => 'Респ Северная Осетия - Алания',
                'federal_district' => 'Северо-Кавказский',
                'kladr_id' => '1500000000000',
                'fias_id' => 'de459e9c-2933-4923-83d1-9c64cfd7a817',
            ),
            15 =>
            array(
                'id' => 16,
                'name' => 'Татарстан',
                'type' => 'Респ',
                'name_with_type' => 'Респ Татарстан',
                'federal_district' => 'Приволжский',
                'kladr_id' => '1600000000000',
                'fias_id' => '0c089b04-099e-4e0e-955a-6bf1ce525f1a',
            ),
            16 =>
            array(
                'id' => 17,
                'name' => 'Тыва',
                'type' => 'Респ',
                'name_with_type' => 'Респ Тыва',
                'federal_district' => 'Сибирский',
                'kladr_id' => '1700000000000',
                'fias_id' => '026bc56f-3731-48e9-8245-655331f596c0',
            ),
            17 =>
            array(
                'id' => 18,
                'name' => 'Удмуртская',
                'type' => 'Респ',
                'name_with_type' => 'Респ Удмуртская',
                'federal_district' => 'Приволжский',
                'kladr_id' => '1800000000000',
                'fias_id' => '52618b9c-bcbb-47e7-8957-95c63f0b17cc',
            ),
            18 =>
            array(
                'id' => 19,
                'name' => 'Хакасия',
                'type' => 'Респ',
                'name_with_type' => 'Респ Хакасия',
                'federal_district' => 'Сибирский',
                'kladr_id' => '1900000000000',
                'fias_id' => '8d3f1d35-f0f4-41b5-b5b7-e7cadf3e7bd7',
            ),
            19 =>
            array(
                'id' => 20,
                'name' => 'Чеченская',
                'type' => 'Респ',
                'name_with_type' => 'Респ Чеченская',
                'federal_district' => 'Северо-Кавказский',
                'kladr_id' => '2000000000000',
                'fias_id' => 'de67dc49-b9ba-48a3-a4cc-c2ebfeca6c5e',
            ),
            20 =>
            array(
                'id' => 21,
                'name' => 'Чувашская Республика -',
                'type' => 'Чувашия',
                'name_with_type' => 'Чувашская Республика - Чувашия',
                'federal_district' => 'Приволжский',
                'kladr_id' => '2100000000000',
                'fias_id' => '878fc621-3708-46c7-a97f-5a13a4176b3e',
            ),
            21 =>
            array(
                'id' => 22,
                'name' => 'Алтайский',
                'type' => 'край',
                'name_with_type' => 'Алтайский край',
                'federal_district' => 'Сибирский',
                'kladr_id' => '2200000000000',
                'fias_id' => '8276c6a1-1a86-4f0d-8920-aba34d4cc34a',
            ),
            22 =>
            array(
                'id' => 23,
                'name' => 'Краснодарский',
                'type' => 'край',
                'name_with_type' => 'Краснодарский край',
                'federal_district' => 'Южный',
                'kladr_id' => '2300000000000',
                'fias_id' => 'd00e1013-16bd-4c09-b3d5-3cb09fc54bd8',
            ),
            23 =>
            array(
                'id' => 24,
                'name' => 'Красноярский',
                'type' => 'край',
                'name_with_type' => 'Красноярский край',
                'federal_district' => 'Сибирский',
                'kladr_id' => '2400000000000',
                'fias_id' => 'db9c4f8b-b706-40e2-b2b4-d31b98dcd3d1',
            ),
            24 =>
            array(
                'id' => 25,
                'name' => 'Приморский',
                'type' => 'край',
                'name_with_type' => 'Приморский край',
                'federal_district' => 'Дальневосточный',
                'kladr_id' => '2500000000000',
                'fias_id' => '43909681-d6e1-432d-b61f-ddac393cb5da',
            ),
            25 =>
            array(
                'id' => 26,
                'name' => 'Ставропольский',
                'type' => 'край',
                'name_with_type' => 'Ставропольский край',
                'federal_district' => 'Северо-Кавказский',
                'kladr_id' => '2600000000000',
                'fias_id' => '327a060b-878c-4fb4-8dc4-d5595871a3d8',
            ),
            26 =>
            array(
                'id' => 27,
                'name' => 'Хабаровский',
                'type' => 'край',
                'name_with_type' => 'Хабаровский край',
                'federal_district' => 'Дальневосточный',
                'kladr_id' => '2700000000000',
                'fias_id' => '7d468b39-1afa-41ec-8c4f-97a8603cb3d4',
            ),
            27 =>
            array(
                'id' => 28,
                'name' => 'Амурская',
                'type' => 'обл',
                'name_with_type' => 'Амурская обл',
                'federal_district' => 'Дальневосточный',
                'kladr_id' => '2800000000000',
                'fias_id' => '844a80d6-5e31-4017-b422-4d9c01e9942c',
            ),
            28 =>
            array(
                'id' => 29,
                'name' => 'Архангельская',
                'type' => 'обл',
                'name_with_type' => 'Архангельская обл',
                'federal_district' => 'Северо-Западный',
                'kladr_id' => '2900000000000',
                'fias_id' => '294277aa-e25d-428c-95ad-46719c4ddb44',
            ),
            29 =>
            array(
                'id' => 30,
                'name' => 'Астраханская',
                'type' => 'обл',
                'name_with_type' => 'Астраханская обл',
                'federal_district' => 'Южный',
                'kladr_id' => '3000000000000',
                'fias_id' => '83009239-25cb-4561-af8e-7ee111b1cb73',
            ),
            30 =>
            array(
                'id' => 31,
                'name' => 'Белгородская',
                'type' => 'обл',
                'name_with_type' => 'Белгородская обл',
                'federal_district' => 'Центральный',
                'kladr_id' => '3100000000000',
                'fias_id' => '639efe9d-3fc8-4438-8e70-ec4f2321f2a7',
            ),
            31 =>
            array(
                'id' => 32,
                'name' => 'Брянская',
                'type' => 'обл',
                'name_with_type' => 'Брянская обл',
                'federal_district' => 'Центральный',
                'kladr_id' => '3200000000000',
                'fias_id' => 'f5807226-8be0-4ea8-91fc-39d053aec1e2',
            ),
            32 =>
            array(
                'id' => 33,
                'name' => 'Владимирская',
                'type' => 'обл',
                'name_with_type' => 'Владимирская обл',
                'federal_district' => 'Центральный',
                'kladr_id' => '3300000000000',
                'fias_id' => 'b8837188-39ee-4ff9-bc91-fcc9ed451bb3',
            ),
            33 =>
            array(
                'id' => 34,
                'name' => 'Волгоградская',
                'type' => 'обл',
                'name_with_type' => 'Волгоградская обл',
                'federal_district' => 'Южный',
                'kladr_id' => '3400000000000',
                'fias_id' => 'da051ec8-da2e-4a66-b542-473b8d221ab4',
            ),
            34 =>
            array(
                'id' => 35,
                'name' => 'Вологодская',
                'type' => 'обл',
                'name_with_type' => 'Вологодская обл',
                'federal_district' => 'Северо-Западный',
                'kladr_id' => '3500000000000',
                'fias_id' => 'ed36085a-b2f5-454f-b9a9-1c9a678ee618',
            ),
            35 =>
            array(
                'id' => 36,
                'name' => 'Воронежская',
                'type' => 'обл',
                'name_with_type' => 'Воронежская обл',
                'federal_district' => 'Центральный',
                'kladr_id' => '3600000000000',
                'fias_id' => 'b756fe6b-bbd3-44d5-9302-5bfcc740f46e',
            ),
            36 =>
            array(
                'id' => 37,
                'name' => 'Ивановская',
                'type' => 'обл',
                'name_with_type' => 'Ивановская обл',
                'federal_district' => 'Центральный',
                'kladr_id' => '3700000000000',
                'fias_id' => '0824434f-4098-4467-af72-d4f702fed335',
            ),
            37 =>
            array(
                'id' => 38,
                'name' => 'Иркутская',
                'type' => 'обл',
                'name_with_type' => 'Иркутская обл',
                'federal_district' => 'Сибирский',
                'kladr_id' => '3800000000000',
                'fias_id' => '6466c988-7ce3-45e5-8b97-90ae16cb1249',
            ),
            38 =>
            array(
                'id' => 39,
                'name' => 'Калининградская',
                'type' => 'обл',
                'name_with_type' => 'Калининградская обл',
                'federal_district' => 'Северо-Западный',
                'kladr_id' => '3900000000000',
                'fias_id' => '90c7181e-724f-41b3-b6c6-bd3ec7ae3f30',
            ),
            39 =>
            array(
                'id' => 40,
                'name' => 'Калужская',
                'type' => 'обл',
                'name_with_type' => 'Калужская обл',
                'federal_district' => 'Центральный',
                'kladr_id' => '4000000000000',
                'fias_id' => '18133adf-90c2-438e-88c4-62c41656de70',
            ),
            40 =>
            array(
                'id' => 41,
                'name' => 'Камчатский',
                'type' => 'край',
                'name_with_type' => 'Камчатский край',
                'federal_district' => 'Дальневосточный',
                'kladr_id' => '4100000000000',
                'fias_id' => 'd02f30fc-83bf-4c0f-ac2b-5729a866a207',
            ),
            41 =>
            array(
                'id' => 42,
                'name' => 'Кемеровская область - Кузбасс',
                'type' => 'обл',
                'name_with_type' => 'Кемеровская область - Кузбасс',
                'federal_district' => 'Сибирский',
                'kladr_id' => '4200000000000',
                'fias_id' => '393aeccb-89ef-4a7e-ae42-08d5cebc2e30',
            ),
            42 =>
            array(
                'id' => 43,
                'name' => 'Кировская',
                'type' => 'обл',
                'name_with_type' => 'Кировская обл',
                'federal_district' => 'Приволжский',
                'kladr_id' => '4300000000000',
                'fias_id' => '0b940b96-103f-4248-850c-26b6c7296728',
            ),
            43 =>
            array(
                'id' => 44,
                'name' => 'Костромская',
                'type' => 'обл',
                'name_with_type' => 'Костромская обл',
                'federal_district' => 'Центральный',
                'kladr_id' => '4400000000000',
                'fias_id' => '15784a67-8cea-425b-834a-6afe0e3ed61c',
            ),
            44 =>
            array(
                'id' => 45,
                'name' => 'Курганская',
                'type' => 'обл',
                'name_with_type' => 'Курганская обл',
                'federal_district' => 'Уральский',
                'kladr_id' => '4500000000000',
                'fias_id' => '4a3d970f-520e-46b9-b16c-50d4ca7535a8',
            ),
            45 =>
            array(
                'id' => 46,
                'name' => 'Курская',
                'type' => 'обл',
                'name_with_type' => 'Курская обл',
                'federal_district' => 'Центральный',
                'kladr_id' => '4600000000000',
                'fias_id' => 'ee594d5e-30a9-40dc-b9f2-0add1be44ba1',
            ),
            46 =>
            array(
                'id' => 47,
                'name' => 'Ленинградская',
                'type' => 'обл',
                'name_with_type' => 'Ленинградская обл',
                'federal_district' => 'Северо-Западный',
                'kladr_id' => '4700000000000',
                'fias_id' => '6d1ebb35-70c6-4129-bd55-da3969658f5d',
            ),
            47 =>
            array(
                'id' => 48,
                'name' => 'Липецкая',
                'type' => 'обл',
                'name_with_type' => 'Липецкая обл',
                'federal_district' => 'Центральный',
                'kladr_id' => '4800000000000',
                'fias_id' => '1490490e-49c5-421c-9572-5673ba5d80c8',
            ),
            48 =>
            array(
                'id' => 49,
                'name' => 'Магаданская',
                'type' => 'обл',
                'name_with_type' => 'Магаданская обл',
                'federal_district' => 'Дальневосточный',
                'kladr_id' => '4900000000000',
                'fias_id' => '9c05e812-8679-4710-b8cb-5e8bd43cdf48',
            ),
            49 =>
            array(
                'id' => 50,
                'name' => 'Московская',
                'type' => 'обл',
                'name_with_type' => 'Московская обл',
                'federal_district' => 'Центральный',
                'kladr_id' => '5000000000000',
                'fias_id' => '29251dcf-00a1-4e34-98d4-5c47484a36d4',
            ),
            50 =>
            array(
                'id' => 51,
                'name' => 'Мурманская',
                'type' => 'обл',
                'name_with_type' => 'Мурманская обл',
                'federal_district' => 'Северо-Западный',
                'kladr_id' => '5100000000000',
                'fias_id' => '1c727518-c96a-4f34-9ae6-fd510da3be03',
            ),
            51 =>
            array(
                'id' => 52,
                'name' => 'Нижегородская',
                'type' => 'обл',
                'name_with_type' => 'Нижегородская обл',
                'federal_district' => 'Приволжский',
                'kladr_id' => '5200000000000',
                'fias_id' => '88cd27e2-6a8a-4421-9718-719a28a0a088',
            ),
            52 =>
            array(
                'id' => 53,
                'name' => 'Новгородская',
                'type' => 'обл',
                'name_with_type' => 'Новгородская обл',
                'federal_district' => 'Северо-Западный',
                'kladr_id' => '5300000000000',
                'fias_id' => 'e5a84b81-8ea1-49e3-b3c4-0528651be129',
            ),
            53 =>
            array(
                'id' => 54,
                'name' => 'Новосибирская',
                'type' => 'обл',
                'name_with_type' => 'Новосибирская обл',
                'federal_district' => 'Сибирский',
                'kladr_id' => '5400000000000',
                'fias_id' => '1ac46b49-3209-4814-b7bf-a509ea1aecd9',
            ),
            54 =>
            array(
                'id' => 55,
                'name' => 'Омская',
                'type' => 'обл',
                'name_with_type' => 'Омская обл',
                'federal_district' => 'Сибирский',
                'kladr_id' => '5500000000000',
                'fias_id' => '05426864-466d-41a3-82c4-11e61cdc98ce',
            ),
            55 =>
            array(
                'id' => 56,
                'name' => 'Оренбургская',
                'type' => 'обл',
                'name_with_type' => 'Оренбургская обл',
                'federal_district' => 'Приволжский',
                'kladr_id' => '5600000000000',
                'fias_id' => '8bcec9d6-05bc-4e53-b45c-ba0c6f3a5c44',
            ),
            56 =>
            array(
                'id' => 57,
                'name' => 'Орловская',
                'type' => 'обл',
                'name_with_type' => 'Орловская обл',
                'federal_district' => 'Центральный',
                'kladr_id' => '5700000000000',
                'fias_id' => '5e465691-de23-4c4e-9f46-f35a125b5970',
            ),
            57 =>
            array(
                'id' => 58,
                'name' => 'Пензенская',
                'type' => 'обл',
                'name_with_type' => 'Пензенская обл',
                'federal_district' => 'Приволжский',
                'kladr_id' => '5800000000000',
                'fias_id' => 'c99e7924-0428-4107-a302-4fd7c0cca3ff',
            ),
            58 =>
            array(
                'id' => 59,
                'name' => 'Пермский',
                'type' => 'край',
                'name_with_type' => 'Пермский край',
                'federal_district' => 'Приволжский',
                'kladr_id' => '5900000000000',
                'fias_id' => '4f8b1a21-e4bb-422f-9087-d3cbf4bebc14',
            ),
            59 =>
            array(
                'id' => 60,
                'name' => 'Псковская',
                'type' => 'обл',
                'name_with_type' => 'Псковская обл',
                'federal_district' => 'Северо-Западный',
                'kladr_id' => '6000000000000',
                'fias_id' => 'f6e148a1-c9d0-4141-a608-93e3bd95e6c4',
            ),
            60 =>
            array(
                'id' => 61,
                'name' => 'Ростовская',
                'type' => 'обл',
                'name_with_type' => 'Ростовская обл',
                'federal_district' => 'Южный',
                'kladr_id' => '6100000000000',
                'fias_id' => 'f10763dc-63e3-48db-83e1-9c566fe3092b',
            ),
            61 =>
            array(
                'id' => 62,
                'name' => 'Рязанская',
                'type' => 'обл',
                'name_with_type' => 'Рязанская обл',
                'federal_district' => 'Центральный',
                'kladr_id' => '6200000000000',
                'fias_id' => '963073ee-4dfc-48bd-9a70-d2dfc6bd1f31',
            ),
            62 =>
            array(
                'id' => 63,
                'name' => 'Самарская',
                'type' => 'обл',
                'name_with_type' => 'Самарская обл',
                'federal_district' => 'Приволжский',
                'kladr_id' => '6300000000000',
                'fias_id' => 'df3d7359-afa9-4aaa-8ff9-197e73906b1c',
            ),
            63 =>
            array(
                'id' => 64,
                'name' => 'Саратовская',
                'type' => 'обл',
                'name_with_type' => 'Саратовская обл',
                'federal_district' => 'Приволжский',
                'kladr_id' => '6400000000000',
                'fias_id' => 'df594e0e-a935-4664-9d26-0bae13f904fe',
            ),
            64 =>
            array(
                'id' => 65,
                'name' => 'Сахалинская',
                'type' => 'обл',
                'name_with_type' => 'Сахалинская обл',
                'federal_district' => 'Дальневосточный',
                'kladr_id' => '6500000000000',
                'fias_id' => 'aea6280f-4648-460f-b8be-c2bc18923191',
            ),
            65 =>
            array(
                'id' => 66,
                'name' => 'Свердловская',
                'type' => 'обл',
                'name_with_type' => 'Свердловская обл',
                'federal_district' => 'Уральский',
                'kladr_id' => '6600000000000',
                'fias_id' => '92b30014-4d52-4e2e-892d-928142b924bf',
            ),
            66 =>
            array(
                'id' => 67,
                'name' => 'Смоленская',
                'type' => 'обл',
                'name_with_type' => 'Смоленская обл',
                'federal_district' => 'Центральный',
                'kladr_id' => '6700000000000',
                'fias_id' => 'e8502180-6d08-431b-83ea-c7038f0df905',
            ),
            67 =>
            array(
                'id' => 68,
                'name' => 'Тамбовская',
                'type' => 'обл',
                'name_with_type' => 'Тамбовская обл',
                'federal_district' => 'Центральный',
                'kladr_id' => '6800000000000',
                'fias_id' => 'a9a71961-9363-44ba-91b5-ddf0463aebc2',
            ),
            68 =>
            array(
                'id' => 69,
                'name' => 'Тверская',
                'type' => 'обл',
                'name_with_type' => 'Тверская обл',
                'federal_district' => 'Центральный',
                'kladr_id' => '6900000000000',
                'fias_id' => '61723327-1c20-42fe-8dfa-402638d9b396',
            ),
            69 =>
            array(
                'id' => 70,
                'name' => 'Томская',
                'type' => 'обл',
                'name_with_type' => 'Томская обл',
                'federal_district' => 'Сибирский',
                'kladr_id' => '7000000000000',
                'fias_id' => '889b1f3a-98aa-40fc-9d3d-0f41192758ab',
            ),
            70 =>
            array(
                'id' => 71,
                'name' => 'Тульская',
                'type' => 'обл',
                'name_with_type' => 'Тульская обл',
                'federal_district' => 'Центральный',
                'kladr_id' => '7100000000000',
                'fias_id' => 'd028ec4f-f6da-4843-ada6-b68b3e0efa3d',
            ),
            71 =>
            array(
                'id' => 72,
                'name' => 'Тюменская',
                'type' => 'обл',
                'name_with_type' => 'Тюменская обл',
                'federal_district' => 'Уральский',
                'kladr_id' => '7200000000000',
                'fias_id' => '54049357-326d-4b8f-b224-3c6dc25d6dd3',
            ),
            72 =>
            array(
                'id' => 73,
                'name' => 'Ульяновская',
                'type' => 'обл',
                'name_with_type' => 'Ульяновская обл',
                'federal_district' => 'Приволжский',
                'kladr_id' => '7300000000000',
                'fias_id' => 'fee76045-fe22-43a4-ad58-ad99e903bd58',
            ),
            73 =>
            array(
                'id' => 74,
                'name' => 'Челябинская',
                'type' => 'обл',
                'name_with_type' => 'Челябинская обл',
                'federal_district' => 'Уральский',
                'kladr_id' => '7400000000000',
                'fias_id' => '27eb7c10-a234-44da-a59c-8b1f864966de',
            ),
            74 =>
            array(
                'id' => 75,
                'name' => 'Забайкальский',
                'type' => 'край',
                'name_with_type' => 'Забайкальский край',
                'federal_district' => 'Дальневосточный',
                'kladr_id' => '7500000000000',
                'fias_id' => 'b6ba5716-eb48-401b-8443-b197c9578734',
            ),
            75 =>
            array(
                'id' => 76,
                'name' => 'Ярославская',
                'type' => 'обл',
                'name_with_type' => 'Ярославская обл',
                'federal_district' => 'Центральный',
                'kladr_id' => '7600000000000',
                'fias_id' => 'a84b2ef4-db03-474b-b552-6229e801ae9b',
            ),
            76 =>
            array(
                'id' => 77,
                'name' => 'Москва',
                'type' => 'г',
                'name_with_type' => 'г Москва',
                'federal_district' => 'Центральный',
                'kladr_id' => '7700000000000',
                'fias_id' => '0c5b2444-70a0-4932-980c-b4dc0d3f02b5',
            ),
            77 =>
            array(
                'id' => 78,
                'name' => 'Санкт-Петербург',
                'type' => 'г',
                'name_with_type' => 'г Санкт-Петербург',
                'federal_district' => 'Северо-Западный',
                'kladr_id' => '7800000000000',
                'fias_id' => 'c2deb16a-0330-4f05-821f-1d09c93331e6',
            ),
            78 =>
            array(
                'id' => 79,
                'name' => 'Еврейская',
                'type' => 'Аобл',
                'name_with_type' => 'Еврейская Аобл',
                'federal_district' => 'Дальневосточный',
                'kladr_id' => '7900000000000',
                'fias_id' => '1b507b09-48c9-434f-bf6f-65066211c73e',
            ),
            79 =>
            array(
                'id' => 80,
                'name' => 'Ненецкий',
                'type' => 'АО',
                'name_with_type' => 'Ненецкий АО',
                'federal_district' => 'Северо-Западный',
                'kladr_id' => '8300000000000',
                'fias_id' => '89db3198-6803-4106-9463-cbf781eff0b8',
            ),
            80 =>
            array(
                'id' => 81,
                'name' => 'Ханты-Мансийский Автономный округ - Югра',
                'type' => 'АО',
                'name_with_type' => 'Ханты-Мансийский Автономный округ - Югра',
                'federal_district' => 'Уральский',
                'kladr_id' => '8600000000000',
                'fias_id' => 'd66e5325-3a25-4d29-ba86-4ca351d9704b',
            ),
            81 =>
            array(
                'id' => 82,
                'name' => 'Чукотский',
                'type' => 'АО',
                'name_with_type' => 'Чукотский АО',
                'federal_district' => 'Дальневосточный',
                'kladr_id' => '8700000000000',
                'fias_id' => 'f136159b-404a-4f1f-8d8d-d169e1374d5c',
            ),
            82 =>
            array(
                'id' => 83,
                'name' => 'Ямало-Ненецкий',
                'type' => 'АО',
                'name_with_type' => 'Ямало-Ненецкий АО',
                'federal_district' => 'Уральский',
                'kladr_id' => '8900000000000',
                'fias_id' => '826fa834-3ee8-404f-bdbc-13a5221cfb6e',
            ),
            83 =>
            array(
                'id' => 84,
                'name' => 'Крым',
                'type' => 'Респ',
                'name_with_type' => 'Респ Крым',
                'federal_district' => 'Южный',
                'kladr_id' => '9100000000000',
                'fias_id' => 'bd8e6511-e4b9-4841-90de-6bbc231a789e',
            ),
            84 =>
            array(
                'id' => 85,
                'name' => 'Севастополь',
                'type' => 'г',
                'name_with_type' => 'г Севастополь',
                'federal_district' => 'Южный',
                'kladr_id' => '9200000000000',
                'fias_id' => '6fdecb78-893a-4e3f-a5ba-aa062459463b',
            ),
            85 =>
            array(
                'id' => 86,
                'name' => 'Байконур',
                'type' => 'г',
                'name_with_type' => 'г Байконур',
                'federal_district' => '',
                'kladr_id' => '9900000000000',
                'fias_id' => '63ed1a35-4be6-4564-a1ec-0c51f7383314',
            ),
        ));
    }
}
