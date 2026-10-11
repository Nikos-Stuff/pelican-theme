<?php

namespace NikosStuff\NsTheme\Filament\Server\Widgets;

use App\Enums\ContainerStatus;
use App\Models\Server;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;
use Illuminate\Support\HtmlString;

class HairlineServerOverview extends Widget
{
    protected string $view = 'hairline::server-overview';

    protected static bool $isLazy = false;

    /** 코어가 서 있던 자리를 그대로 차지한다 — 3열 그리드에서 1열만 먹으면 오른쪽이 빈다(실측 피드백). */
    protected int | string | array $columnSpan = 'full';

    public const UNKNOWN = '—';

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        return [
            'name' => $server->name,
            'status' => $this->statusHtml($server),
            'statusColor' => $this->statusColor($server),
            'address' => $server->allocation?->address ?? '-',
            'cpu' => $this->cpuUsageHtml($server),
            'memory' => $this->memoryUsageHtml($server),
            'disk' => $this->diskUsageHtml($server),
        ];
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