<?php

/**
 * Array of reclamation status.
 */


use App\Contracts\Shop\Reclamations\ReclamationStatusInterface;

return [

    ReclamationStatusInterface::REGISTERED => ['title_en' => 'Registered', 'title_ru' => 'Зарегистрирован', 'title_ua' => 'Зареєстрований', 'badge_class' => 'info'],

    ReclamationStatusInterface::TESTING => ['title_en' => 'Testing', 'title_ru' => 'Проверяется', 'title_ua' => 'Перевіряється', 'badge_class' => 'info'],

    ReclamationStatusInterface::ACCEPTED => ['title_en' => 'Accepted', 'title_ru' => 'Принят', 'title_ua' => 'Прийнятий', 'badge_class' => 'warning'],

    ReclamationStatusInterface::NOT_ACCEPTED => ['title_en' => 'Not accepted', 'title_ru' => 'Не принят', 'title_ua' => 'Не прийнятий', 'badge_class' => 'alert'],

    ReclamationStatusInterface::EXCHANGED => ['title_en' => 'Exchanged', 'title_ru' => 'Обменян', 'title_ua' => 'Обміняний', 'badge_class' => 'success'],

    ReclamationStatusInterface::WRITE_OFF => ['title_en' => 'Write off', 'title_ru' => 'Списан', 'title_ua' => 'Списаний', 'badge_class' => 'success'],

    ReclamationStatusInterface::RETURNED => ['title_en' => 'Returned', 'title_ru' => 'Возвращен', 'title_ua' => 'повернутий', 'badge_class' => 'alert'],

    ReclamationStatusInterface::LOSSES => ['title_en' => 'Losses', 'title_ru' => 'Убытки', 'title_ua' => 'Збитки', 'badge_class' => 'alert'],


];