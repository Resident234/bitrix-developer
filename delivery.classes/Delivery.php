<?php

class Delivery
{
    private $id, $name, $code, $price, $description, $sort, $isOpt, $isRetail;

    function __construct($array)
    {
        $this->id = $array['ID'];
        list(, $code) = explode('#', $array['NAME']);
        $this->code = $code;
        $this->name = str_replace('#' . $code . '#', '', $array['NAME']);
        $this->price = $array['PRICE'];
        $this->description = $array['DESCRIPTION'];
        $this->sort = $array['SORT'];
        if (in_array($this->id, DeliveriesList::getRetailDeliveries())) {
            $this->isRetail = true;
        } else {
            $this->isRetail = false;
        }
        if (in_array($this->id, DeliveriesList::getOptDeliveries())) {
            $this->isOpt = true;
        } else {
            $this->isOpt = false;
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getSort()
    {
        return $this->sort;
    }

    public function getIsOpt()
    {
        return $this->isOpt;
    }

    public function getIsRetail()
    {
        return $this->isRetail;
    }

    public function getFields()
    {
        return array(
            'ID' => $this->getId(),
            'NAME' => $this->getName(),
            'CODE' => $this->getCode(),
            'PRICE' => $this->getPrice(),
            'DESC' => $this->getDescription(),
            'SORT' => $this->getSort(),
            'IS_OPT' => $this->getIsOpt(),
            'IS_RETAIL' => $this->getIsRetail()
        );
    }
}