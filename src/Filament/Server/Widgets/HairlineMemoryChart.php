<?php

namespace NikosStuff\NsTheme\Filament\Server\Widgets;

use App\Filament\Server\Widgets\ServerMemoryChart;

/** Themed Memory chart. Data still comes from the parent; colors/options from the trait. */
class HairlineMemoryChart extends ServerMemoryChart
{
  use HairlineChart;

  protected function chartColor(): string
  {
    return 'rgb(16, 185, 129)';
  }
}
