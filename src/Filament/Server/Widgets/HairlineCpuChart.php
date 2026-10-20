<?php

namespace NikosStuff\NsTheme\Filament\Server\Widgets;

use App\Filament\Server\Widgets\ServerCpuChart;

/** Themed CPU chart. Data still comes from the parent; colors/options from the trait. */
class HairlineCpuChart extends ServerCpuChart
{
  use HairlineChart;

  protected function chartColor(): string
  {
    return 'rgb(0, 111, 255)';
  }
}
