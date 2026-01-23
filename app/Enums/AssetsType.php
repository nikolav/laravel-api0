<?php

namespace App\Enums;

enum AssetsType: string
{

  // people
  case People_Group     = '3f21dedb-3b37-5baa-82e0-0610fafed17d';
  // physical
  case Physical_Product = '61a710e5-7fd9-58f2-aaa7-11d16a276e76';
  case Physical_Store   = '34f10445-6150-59e4-93e2-a716ac782d78';
  // digital
  case Digital_Post     = '85f212e1-6cd3-55e0-9d09-bfbcf751fe88';

  public function label(): string
  {
    return match ($this) {
      self::People_Group     => 'People:Group',
      self::Physical_Product => 'Physical:Product',
      self::Physical_Store   => 'Physical:Store',
      self::Digital_Post     => 'Digital:Post',
    };
  }
}
