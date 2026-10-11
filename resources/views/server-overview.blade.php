<x-filament-widgets::widget>
<div 
    x-data="hairlineServerOverview({
        server: @js([
            'id' => $this->getWidgetServer()->id,
            'uuid' => $this->getWidgetServer()->uuid,
            'name' => $name,
            'address' => $address,
            'cpuLimit' => $this->getWidgetServer()->cpu,
            'memoryLimit' => $this->getWidgetServer()->memory,
            'diskLimit' => $this->getWidgetServer()->disk,
            'initialStatus' => $this->getWidgetServer()->retrieveStatus()?->value ?? 'offline',
            'initialStatusLabel' => $this->getWidgetServer()->condition?->getLabel() ?? 'Offline',
        ]),
        useBinaryPrefix: @js(config('panel.use_binary_prefix', true))
    })"
    x-init="init()"
    class="hl-overview"
>
    <table class="hl-table">
        <tbody>
            <tr>
                <th>{{ trans('server/console.labels.name') }}</th>
                <td><span x-text="server.name"></span></td>
                <th>{{ trans('server/console.labels.cpu') }}</th>
                <td><span x-text="formattedCpu"></span></td>
            </tr>
            <tr>
                <th>{{ trans('server/console.labels.status') }}</th>
                <td>
                    <span class="hl-dot" :class="'hl-' + statusColor"></span>
                    <span x-text="formattedStatus"></span>
                </td>
                <th>{{ trans('server/console.labels.memory') }}</th>
                <td><span x-text="formattedMemory"></span></td>
            </tr>
            <tr>
                <th>{{ trans('server/console.labels.address') }}</th>
                <td><code class="hl-mono" x-text="server.address"></code></td>
                <th>{{ trans('server/console.labels.disk') }}</th>
                <td><span x-text="formattedDisk"></span></td>
            </tr>
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('hairlineServerOverview', ({ server, useBinaryPrefix }) => ({
            server,
            useBinaryPrefix,
            status: server.initialStatus,
            statusLabel: server.initialStatusLabel,
            stats: {
                cpu: 0,
                memory: 0,
                disk: 0,
                uptime: 0,
            },

            init() {
                if (window.Echo) {
                    window.Echo.private(`server.${this.server.uuid}`)
                        .listen('.stats', (e) => {
                            this.stats.cpu = e.cpu_absolute ?? 0;
                            this.stats.memory = e.memory_bytes ?? 0;
                            this.stats.disk = e.disk_bytes ?? 0;
                            this.stats.uptime = e.uptime ?? 0;
                            if (e.status) {
                                this.status = e.status;
                            }
                        });
                }
            },

            get isOffline() {
                return this.status === 'offline' || this.status === 'stopping';
            },

            get statusColor() {
                if (this.status === 'running') return 'ok';
                if (this.isOffline) return 'off';
                return 'busy';
            },

            get formattedStatus() {
                let label = this.statusLabel || this.status;
                if (this.stats.uptime > 0 && !this.isOffline) {
                    const seconds = Math.floor(this.stats.uptime / 1000);
                    const d = Math.floor(seconds / 86400);
                    const h = Math.floor((seconds % 86400) / 3600);
                    const m = Math.floor((seconds % 3600) / 60);

                    let timeStr = '';
                    if (d > 0) timeStr = `${d}d ${h}h`;
                    else if (h > 0) timeStr = `${h}h ${m}m`;
                    else timeStr = `${m}m`;

                    return `${label} · ${timeStr}`;
                }
                return label;
            },

            get formattedCpu() {
                if (this.isOffline) return '-';
                const cpuStr = this.stats.cpu.toFixed(2) + ' %';
                return this.server.cpuLimit > 0 
                    ? `${cpuStr} / ${this.server.cpuLimit} %` 
                    : cpuStr;
            },

            get formattedMemory() {
                if (this.isOffline) return '-';
                const used = this.formatBytes(this.stats.memory);
                const total = this.server.memoryLimit * (this.useBinaryPrefix ? 1024 * 1024 : 1000 * 1000);
                return this.server.memoryLimit > 0 
                    ? `${used} / ${this.formatBytes(total)}` 
                    : used;
            },

            get formattedDisk() {
                if (this.stats.disk === 0) return '-';
                const used = this.formatBytes(this.stats.disk);
                const total = this.server.diskLimit * (this.useBinaryPrefix ? 1024 * 1024 : 1000 * 1000);
                return this.server.diskLimit > 0 
                    ? `${used} / ${this.formatBytes(total)}` 
                    : used;
            },

            formatBytes(bytes) {
                if (!bytes || bytes === 0) return '0 B';
                const k = this.useBinaryPrefix ? 1024 : 1000;
                const sizes = this.useBinaryPrefix 
                    ? ['B', 'KiB', 'MiB', 'GiB', 'TiB'] 
                    : ['B', 'KB', 'MB', 'GB', 'TB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }
        }));
    });
</script>
</x-filament-widgets::widget>
