<?php

namespace App\Http\Controllers\System;

class Sort
{
    private $data;
    private $registry = [];

    public function __construct($sortData) {
        $this->data = $sortData;
    }

    /**
     * Проверка соответствия подаваемого знака списку
     *
     * @param $sign Символ знака
     * @return bool
     */
    private function check(string $sign) {
        return in_array($sign, ['-', '+']);
    }

    /**
     * Установка знака операции в sql-формат направления
     *
     * @param $sign Символ знака
     * @return string
     */
    protected function setSign($char) {
        return $char == '+' ? 'ASC' : 'DESC';
    }

    /**
     * Сборка массива параметров
     *
     * @return this
     */
    public function parce() {
        foreach ($this->data as $sortItem) {
            $part = [];
            if ($this->check($sortItem[0])) {
                $sign = $sortItem[0];
                $part[] = str_replace($sign, '', $sortItem);
                $part[] = $this->setSign($sign);
                $this->registry[] = $part;
            }
        }
        return $this;
    }

    /**
     * Геттер для списка параметров сортировки
     *
     * @return array
     */
    public function get() {
        return $this->registry;
    }

    /**
     * Преобразование параметров сортировки в raw формат
     *
     * @return string
     */
    public function getRaw() {
        $fields = [];
        foreach ($this->registry as $predicates) {
            $fields[] = implode(' ', $predicates);
        }
        return implode(', ', $fields);
    }
}