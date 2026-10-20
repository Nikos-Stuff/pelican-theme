<?php

namespace NikosStuff\NsTheme\Filament\Server\Widgets;

use App\Filament\Server\Widgets\ServerNetworkChart;

/** Themed Network chart. Data still comes from the parent; colors/options from the trait. */
class HairlineNetworkChart extends ServerNetworkChart
{
  use HairlineChart;

  protected function chartColor(): string
  {
    return 'rgb(245, 165, 36)';
  }
}
