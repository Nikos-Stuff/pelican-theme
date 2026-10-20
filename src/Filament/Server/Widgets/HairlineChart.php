<?php

namespace NikosStuff\NsTheme\Filament\Server\Widgets;

use Filament\Support\RawJs;
use Livewire\Attributes\On;

/**
 * Shared styling for the console charts.
 *
 * Chart.js lives inside Filament’s Alpine component, so there is no window.Chart
 * and CSS cannot touch the lines. Colors and options must go through getData()
 * and getOptions().
 *
 * After PR #2558 the core charts no longer read the cache. Live data arrives
 * as an `updateChartData` Livewire event that is only dispatched to the
 * original ServerCpuChart / ServerMemoryChart / ServerNetworkChart components.
 * This trait listens for the same event so a subclass can receive it once we
 * forward the event (see the JS snippet).
 */
trait HairlineChart
{
  /** Line color. Defined by the child class. */
  abstract protected function chartColor(): string;

  /** @return array<string, mixed> */
  protected function getData(): array
  {
    $data = parent::getData();

    $color = $this->chartColor();

    foreach ($data['datasets'] ?? [] as $i => $dataset) {
      $data['datasets'][$i] = array_merge($dataset, [
        'borderColor' => $color,
        // Soft fill under the line.
        'backgroundColor' => str_replace('rgb(', 'rgba(', rtrim($color, ')')) . ', 0.12)',
        'borderWidth' => 1.5,
        'tension' => 0.35,
        'fill' => true,
        'pointRadius' => 0,
        'pointHoverRadius' => 3,
      ]);
    }

    return $data;
  }

  protected function getOptions(): RawJs
  {
    return RawJs::make(<<<'JS'
        {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 250 },
            interaction: { intersect: false, mode: 'index' },
            scales: {
                y: {
                    min: 0,
                    border: { display: false },
                    grid: { color: 'rgba(124,130,140,0.16)', drawTicks: false, borderDash: [3, 3] },
                    ticks: { font: { size: 10 }, color: '#9ba1aa', maxTicksLimit: 4, padding: 6 },
                },
                x: { display: false },
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    displayColors: false,
                    backgroundColor: 'rgba(20,22,26,0.92)',
                    padding: 8,
                    titleFont: { size: 11 },
                    bodyFont: { size: 11 },
                },
            },
        }
        JS);
  }

  /**
   * Receives the same payload the core charts get.
   * Filament’s ChartWidget already knows how to apply `updateChartData`;
   * we just need the event to reach this component.
   */
  #[On('updateChartData')]
  public function updateChartData(array $data): void
  {
    // ChartWidget stores the data and re-renders the Alpine chart.
    // Calling the parent implementation (if it exists) or letting the
    // framework handle the event is enough.
    if (method_exists(parent::class, 'updateChartData')) {
      parent::updateChartData($data);
    }
  }
}
