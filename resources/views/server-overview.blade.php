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

  {{-- Hosting order: status, plan, renewal, expiry --}}
  <div class="hl-overview" style="margin-top: 1rem;">
    <table class="hl-table">
      <tbody>
        <tr>
          <th>Order</th>
          <td>
            <span class="hl-dot hl-{{ $orderStatusColor }}"></span>{{ $orderStatus }}
          </td>
          <th>Plan</th>
          <td>{{ $orderSubplan }}</td>
        </tr>
        <tr>
          <th>Renewal</th>
          <td>
            @if ($orderExpiryAt && strtolower($orderSubplan) !== 'free')
            <span class="hl-renewal" data-expiry="{{ $orderExpiryAt }}">—</span>
            @else
            —
            @endif
          </td>
          <th>Expires</th>
<td>
  @if ($orderExpiryAt)
    <span class="hl-expiry" data-expiry="{{ $orderExpiryAt }}">—</span>
  @else
    —
  @endif

  @if (strtolower($orderSubplan) === 'free')
    <x-filament::button
      tag="a"
      href="https://hosting.nikostuff.com/extendfreeserver?id={{ $serverUuid }}"
      size="sm"
      color="gray"
      outlined
      class="ms-2 align-middle"
      target="_blank"
    >
      Extend
    </x-filament::button>
  @endif
</td>
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

  const GRACE_DAYS = 4;

  const setDot = (state) => {
    const cell = document.querySelector('.hl-overview .hl-dot');
    if (!cell) return;
    cell.className = `hl-dot hl-${DOT_MAP[state] ?? 'busy'}`;
  };

  Livewire.on('console-status', ({
    state
  }) => {
    if (state) setDot(state);
  });

  const pad = (n) => String(n).padStart(2, '0');

  const formatRemaining = (ms) => {
    if (ms <= 0) return 'Expired';

    const totalSec = Math.floor(ms / 1000);
    const days = Math.floor(totalSec / 86400);
    const hours = Math.floor((totalSec % 86400) / 3600);
    const mins = Math.floor((totalSec % 3600) / 60);
    const secs = totalSec % 60;

    if (days >= 1) {
      return `${days}d ${hours}h remaining`;
    }

    return `${pad(hours)}:${pad(mins)}:${pad(secs)} remaining`;
  };

  const formatAbsoluteLocal = (ms) =>
    new Date(ms).toLocaleString(undefined, {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      hour: '2-digit',
      minute: '2-digit',
      hour12: false,
    });

  const tickExpiry = () => {
    document.querySelectorAll('.hl-expiry[data-expiry], .hl-renewal[data-expiry]').forEach((el) => {
      const end = Date.parse(el.dataset.expiry);
      if (Number.isNaN(end)) return;

      const isRenewal = el.classList.contains('hl-renewal');
      const target = isRenewal ? end - GRACE_DAYS * 86400000 : end;

      const absolute = formatAbsoluteLocal(target);
      const remaining = formatRemaining(target - Date.now());

      el.textContent = `${absolute} (${remaining})`;
    });
  };

  tickExpiry();
  setInterval(tickExpiry, 1000);
</script>
@endscript