<?php
namespace App\Enums\Order;
enum OrderStatus :int{
    case DRAFT = 0;
    case CONFIRM = 1;
    case CHECKOUT = 2;
    case IN_CART= 3;

    public static function values(){
        return  array_column(self::cases(), 'value');
    }

}
