{{-- Attribute table (#61). One table instead of six core cards — core ServerOverview is hidden via CSS.

     Must wrap in <x-filament-widgets::widget> so getColumnSpan() becomes grid-column
     (widget.blade: $attributes->gridColumn(...)). Without it, columnSpan='full' has nowhere
     to apply and the widget only takes one grid column.

     Live metrics (status / cpu / memory / disk) are filled by the console WebSocket
     via the same element IDs core ServerOverview uses. Do NOT wire:poll or read
     servers.{id}.* cache keys — those are no longer written (PR #2558).

     Hosting order block (status / plan / expiry) comes from the external `hosting`
     connection (HairlineProvider), matched on orders.pelican_server_id. --}}
<x-filament-widgets::widget>
  <div class="hl-overview">
    <table class="hl-table">
      <tbody>
        <tr>
          <th>{{ trans('server/console.labels.name') }}</th>
          <td>{{ $name }}</td>
          <th>{{ trans('server/console.labels.cpu') }}</th>
          <td>{!! $cpu !!}</td>
        </tr>
        <tr>
          <th>{{ trans('server/console.labels.status') }}</th>
          <td><span class="hl-dot hl-{{ $statusColor }}"></span>{!! $status !!}</td>
          <th>{{ trans('server/console.labels.memory') }}</th>
          <td>{!! $memory !!}</td>
        </tr>
        <tr>
          <th>{{ trans('server/console.labels.address') }}</th>
          <td><code class="hl-mono">{{ $address }}</code></td>
          <th>{{ trans('server/console.labels.disk') }}</th>
          <td>{!! $disk !!}</td>
        </tr>
      </tbody>
    </table>
  </div>

  {{-- Hosting order: status, plan, expiry (separate table, same hairline style) --}}
  <div class="hl-overview" style="margin-top: 0.5rem;">
    <table class="hl-table">
      <tbody>
        <tr>
          <th>Status</th>
          <td>{{ $orderStatus }}</td>
          <th>Plan</th>
          <td>{{ $orderSubplan }}</td>
        </tr>
        <tr>
          <th>Expires</th>
          <td colspan="3">{{ $orderExpiry }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</x-filament-widgets::widget>

@script
<script>
  const DOT_MAP = {
    running: 'ok',
    starting: 'busy',
    stopping: 'busy',
    offline: 'off',
  };

  const setDot = (state) => {
    // Only the first (metrics) overview table has the live status dot.
    const cell = document.querySelector('.hl-overview .hl-dot');
    if (!cell) return;
    cell.className = `hl-dot hl-${DOT_MAP[state] ?? 'busy'}`;
  };

  Livewire.on('console-status', ({
    state
  }) => {
    if (state) setDot(state);
  });
</script>
@endscript