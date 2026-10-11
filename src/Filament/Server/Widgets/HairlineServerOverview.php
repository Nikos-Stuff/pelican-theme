<?php

namespace NikosStuff\NsTheme\Filament\Server\Widgets;

use App\Models\Server;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;

/**
 * 콘솔 상단 오버뷰를 **표 형태**로 (#61).
 *
 * 코어 ServerOverview 는 스탯 카드 6개를 나열한다 — 속성 표로 바꾼다. 코어 위젯은
 * CSS 로 숨기고 이 위젯이 같은 자리(AboveConsole)에 선다.
 * Pelican v1.0.0+부터 스탯은 cache() 대신 웹소켓(Echo)을 통해 프론트엔드로 직접 스트리밍됩니다.
 */
class HairlineServerOverview extends Widget
{
    protected string $view = 'hairline::server-overview';

    protected static bool $isLazy = false;

    /** 코어가 서 있던 자리를 그대로 차지한다 — 3열 그리드에서 1열만 먹으면 오른쪽이 빈다(실측 피드백). */
    protected int | string | array $columnSpan = 'full';

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        $useBinary = config('panel.use_binary_prefix', true);
        $multiplier = $useBinary ? 1024 * 1024 : 1000 * 1000;

        return [
            'server' => [
                'id' => $server->id,
                'uuid' => $server->uuid,
                'name' => $server->name,
                'address' => $server->allocation?->address ?? '-',
                'cpuLimit' => $server->cpu,
                'memoryLimit' => $server->memory * $multiplier,
                'diskLimit' => $server->disk * $multiplier,
                'initialStatus' => $server->retrieveStatus()?->value ?? 'offline',
                'initialStatusLabel' => $server->condition?->getLabel() ?? 'Offline',
            ],
            'useBinaryPrefix' => $useBinary,
        ];
    }
}
