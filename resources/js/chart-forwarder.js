document.addEventListener('livewire:initialized', () => {
  const forward = () => {
    if (!window.ServerStats) return;

    Livewire.dispatchTo(
      'nikos-stuff.ns-theme.filament.server.widgets.hairline-cpu-chart',
      'updateChartData',
      { data: window.ServerStats.cpuData() }
    );
    Livewire.dispatchTo(
      'nikos-stuff.ns-theme.filament.server.widgets.hairline-memory-chart',
      'updateChartData',
      { data: window.ServerStats.memoryData() }
    );
    Livewire.dispatchTo(
      'nikos-stuff.ns-theme.filament.server.widgets.hairline-network-chart',
      'updateChartData',
      { data: window.ServerStats.networkData() }
    );
  };

  setInterval(forward, 1000);
});
