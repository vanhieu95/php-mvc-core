<?php

namespace app\core;

use app\core\db\ActiveRecord;

abstract class User extends ActiveRecord
{
  abstract public function getDisplayName();
}
