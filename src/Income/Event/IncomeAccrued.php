<?php

declare(strict_types=1);

namespace App\Income\Event;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class IncomeAccrued extends GenericEvent
{
}