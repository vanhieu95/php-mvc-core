<?php

namespace VanHieu\PhpMvcCore;

use VanHieu\PhpMvcCore\db\ActiveRecord;

abstract class User extends ActiveRecord
{
  abstract public function getDisplayName();
}
