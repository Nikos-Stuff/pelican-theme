<?php

namespace NikosStuff\NsTheme\Providers;

use App\Enums\ConsoleWidgetPosition;
use App\Filament\Server\Pages\Console;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use NikosStuff\NsTheme\Filament\Server\Widgets\HairlineCpuChart;
use NikosStuff\NsTheme\Filament\Server\Widgets\HairlineMemoryChart;
use NikosStuff\NsTheme\Filament\Server\Widgets\HairlineNetworkChart;
use NikosStuff\NsTheme\Filament\Server\Widgets\HairlineServerOverview;


class HairlineProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->booted(fn () => $this->registerPalette());

        FilamentView::registerRenderHook(
            PanelsRenderHook::STYLES_AFTER,
            fn (): string => $this->styles(),
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::SCRIPTS_BEFORE,
            fn (): string => $this->consoleFontScript(),
        );

        $this->loadViewsFrom(dirname(__DIR__, 2) . '/resources/views', 'hairline');

        View::prependLocation(dirname(__DIR__, 2) . '/resources/views-override');
        Console::registerCustomWidgets(ConsoleWidgetPosition::Top, [HairlineServerOverview::class]);

        FilamentView::registerRenderHook(
            PanelsRenderHook::SCRIPTS_AFTER, 
            fn (): string => $this->buttonsFxScript(),
        );
    }

    private function registerPalette(): void
    {
        // ── 시그니처 블루. Filament 이 shade 별로 골라 쓰므로 전 단계를 준다.
        FilamentColor::register([
            'primary' => [
                50 => '#e5f0ff',
                100 => '#cce1ff',
                200 => '#99c3ff',
                300 => '#66a5ff',
                400 => '#3387ff',
                500 => '#006fff',   // ← 시그니처 블루
                600 => '#005ce6',
                700 => '#0049b8',
                800 => '#00378a',
                900 => '#00255c',
                950 => '#001737',
            ],
            // ── 그래파이트 스케일. 다크 모드의 배경·카드·보더가 전부 이 스케일에서 나온다.
            //    순수 회색이 아니라 살짝 푸른 그래파이트다.
            'gray' => [
                50 => '#f7f8fa',
                100 => '#eef0f3',
                200 => '#dfe2e7',
                300 => '#c3c8d0',
                400 => '#9ba1aa',
                500 => '#7c828c',
                600 => '#5c626c',
                700 => '#3a3f47',   // 보더
                800 => '#252930',   // 카드
                900 => '#1b1e24',   // 콘텐츠 배경
                950 => '#121418',   // 사이드바(거의 검정)
            ],
            'success' => Color::hex('#38cc65'),
            'warning' => Color::hex('#f5a524'),
            'danger' => Color::hex('#f0383b'),
            'info' => Color::hex('#00b2ff'),
        ]);
    }

    private function styles(): string
    {
        $css = @file_get_contents(dirname(__DIR__, 2) . '/resources/theme.css');

        return $css === false ? '' : '<style data-hairline>' . $css . '</style>';
    }

    private function consoleFontScript(): string
    {
        $js = @file_get_contents(dirname(__DIR__, 2) . '/resources/console-font.js');

        return $js === false ? '' : '<script data-hairline>' . $js . '</script>';
    }

    private function buttonsFxScript(): string
    {
        $js = @file_get_contents(dirname(__DIR__, 2) . '/resources/js/buttons_fx.js');
        return $js === false ? '' : '<script data-hairline>' . $js . '</script>';
    }
}
