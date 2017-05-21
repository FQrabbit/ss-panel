<?php


namespace App\Models;


class Shop extends Model
{
    protected $table = "shop";

    public function isByTime()
    {
    	return $this->attributes['type'] == 'A';
    }

    public function isByMete()
    {
    	return $this->attributes['type'] == 'B';
    }

    public function unlimitTransfer()
    {
    	return $this->transfer == 999;
    }
}