<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2019/2/18
 * Time: 9:34 AM
 */

namespace CityBrain\Core;

use Throwable;

class CityBrainException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}