<?php

namespace App\Enums;

enum AssetsType: string
{
  // people
  case People_Group         = '3f21dedb-3b37-5baa-82e0-0610fafed17d';
  case People_Employee      = 'a28b1277-21a5-597b-9f70-a889496ebfe3';
  case People_Person        = 'b51e7306-fa19-55a3-a1a2-fe039d4e8619';
    // physical
  case Physical_Product     = '61a710e5-7fd9-58f2-aaa7-11d16a276e76';
  case Physical_Store       = '34f10445-6150-59e4-93e2-a716ac782d78';
  case Physical_Document    = '74063c22-a8ab-55f5-9b17-f8e3079fea73';
    // digital
  case Digital_Post         = '85f212e1-6cd3-55e0-9d09-bfbcf751fe88';
    // authorization
  case Security_Policy      = 'fddd790d-f056-56c9-9437-2931bf8c7c31';
  case Security_Role        = 'e99db6ff-7f43-5860-ab0d-3a1485ed25c6';
  case Security_Permission  = '84ccba4e-a9df-55ec-b5e0-c97099129718';

  function label(): string
  {
    return match ($this) {
      self::People_Group        => 'People:Group',
      self::People_Employee     => 'People:Employee',
      self::People_Person       => 'People:Person',
      self::Physical_Product    => 'Physical:Product',
      self::Physical_Store      => 'Physical:Store',
      self::Physical_Document   => 'Physical:Document',
      self::Digital_Post        => 'Digital:Post',
      self::Security_Policy     => 'Security:Policy',
      self::Security_Role       => 'Security:Role',
      self::Security_Permission => 'Security:Permission',
    };
  }
}
