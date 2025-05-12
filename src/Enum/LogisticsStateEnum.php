<?php

namespace Kuaidi100QueryBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 运单快递状态
 * TODO 按照文档补全
 *
 * @see https://api.kuaidi100.com/document/5f0ffb5ebc8da837cbd8aefc
 */
enum LogisticsStateEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case ONWAY = '0';
    case PICKUP = '1';
    case DELIVER = '5';
    case SIGN = '3';
    case RETURN = '6';

    public function getLabel(): string
    {
        return match ($this) {
            self::PICKUP => '揽收',
            self::ONWAY => '在途',
            self::DELIVER => '派件',
            self::SIGN => '签收',
            self::RETURN => '退回',
        };
    }
}
