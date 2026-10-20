<?php

namespace NikosStuff\NsTheme\Filament\Server\Widgets;

use App\Enums\ContainerStatus;
use App\Models\Server;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class HairlineServerOverview extends Widget
{
  protected string $view = 'hairline::server-overview';

  protected static bool $isLazy = false;

  /** Occupy the full console top grid so the right side is not empty. */
  protected int | string | array $columnSpan = 'full';

  public const UNKNOWN = '—';

  /** @return array<string, mixed> */
  protected function getViewData(): array
  {
    /** @var Server $server */
    $server = Filament::getTenant();

    // Order row from the external hosting_orders DB (registered in HairlineProvider).
    $order = $this->orderFor($server);

    return [
      'name' => $server->name,
      'status' => $this->statusHtml($server),
      'statusColor' => $this->statusColor($server),
      'address' => $server->allocation?->address ?? '-',
      'cpu' => $this->cpuUsageHtml($server),
      'memory' => $this->memoryUsageHtml($server),
      'disk' => $this->diskUsageHtml($server),

      // Hosting order fields (separate table under the metrics overview).
      'orderStatus' => $this->formatLabel($order?->status),
      'orderSubplan' => $this->formatLabel($order?->subplan),
      'orderExpiry' => $this->formatExpiry($order?->expiry_date),
      'orderExpiryAt' => $order?->expiry_date
        ? \Carbon\Carbon::parse($order->expiry_date)
        ->timezone(config('app.timezone'))
        ->toIso8601String()
        : null,
    ];
  }

  /**
   * Latest order linked to this Pelican server.
   * Matched on orders.pelican_server_id === servers.id.
   */
  private function orderFor(Server $server): ?object
  {
    try {
      return cache()->remember(
        "hosting.order.{$server->id}",
        now()->addMinutes(5),
        fn() => DB::connection('hosting')
          ->table('orders')
          ->where('pelican_server_id', $server->id)
          ->orderByDesc('id')
          ->first()
      );
    } catch (\Throwable) {
      return null;
    }
  }

  /** free → Free, active → Active; null/empty → — */
  private function formatLabel(?string $value): string
  {
    if ($value === null || $value === '') {
      return self::UNKNOWN;
    }

    return Str::ucfirst(strtolower($value));
  }

  /** Format expiry for display; null → — */
  private function formatExpiry(mixed $value): string
  {
    if ($value === null || $value === '') {
      return self::UNKNOWN;
    }

    return Carbon::parse($value)
      ->timezone(config('app.timezone'))
      ->format('Y-m-d H:i');
  }

  private function statusHtml(Server $server): HtmlString
  {
    // Console JS writes into #server-stat-status (status + optional uptime).
    return new HtmlString(
      '<span id="server-stat-status">' . e($server->condition->getLabel()) . '</span>'
    );
  }

  private function statusColor(Server $server): string
  {
    return match (true) {
      $server->retrieveStatus() === ContainerStatus::Running => 'ok',
      $server->retrieveStatus()->isOffline() => 'off',
      default => 'busy',
    };
  }

  private function cpuUsageHtml(Server $server): HtmlString
  {
    $limit = $server->cpu > 0
      ? ' / ' . format_number($server->cpu) . ' %'
      : '';

    // Console JS: setUsage('server-stat-cpu', ...) appends data-limit.
    return new HtmlString(
      '<span id="server-stat-cpu" data-limit="' . e($limit) . '">'
        . self::UNKNOWN . e($limit)
        . '</span>'
    );
  }

  private function memoryUsageHtml(Server $server): HtmlString
  {
    $total = $server->memory * (config('panel.use_binary_prefix') ? 1024 * 1024 : 1000 * 1000);
    $limit = $server->memory > 0
      ? ' / ' . convert_bytes_to_readable($total)
      : '';

    return new HtmlString(
      '<span id="server-stat-memory" data-limit="' . e($limit) . '">'
        . self::UNKNOWN . e($limit)
        . '</span>'
    );
  }

  private function diskUsageHtml(Server $server): HtmlString
  {
    $total = $server->disk * (config('panel.use_binary_prefix') ? 1024 * 1024 : 1000 * 1000);
    $limit = $server->disk > 0
      ? ' / ' . convert_bytes_to_readable($total)
      : '';

    // Disk is updated without data-limit; limit is rendered as sibling text (same as core).
    return new HtmlString(
      '<span id="server-stat-disk">' . self::UNKNOWN . '</span>' . e($limit)
    );
  }
}
