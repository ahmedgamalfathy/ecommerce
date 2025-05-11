<?php
namespace App\Enums\Order;
enum OrderStatus :int{
    case DRAFT = 0;
    case CONFIRM = 1;

    public static function values(){
        return  array_column(self::cases(), 'value');
    }

}
