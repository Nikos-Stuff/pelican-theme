<footer class="relative mt-auto">
  <div class="border-t border-[#eef0f3] dark:border-[#2c2f36]">
    {{-- Back to top --}}
    <div class="py-4 flex justify-center sm:justify-end px-4">
      <button
        type="button"
        onclick="window.scrollTo({ top: 0, behavior: 'smooth' })"
        class="inline-flex items-center gap-1.5 text-xs text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors"
        aria-label="Back to top">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="rotate-90">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        Back to top
      </button>
    </div>

    <div class="border-t border-[#eef0f3] dark:border-[#2c2f36] py-5 px-4">
      <div class="max-w-5xl mx-auto flex flex-col items-center sm:items-start gap-2 text-xs text-gray-500 dark:text-gray-400">
        <div>
          Created &amp; Built with ❤️ by
          <a href="https://dc.nikostuff.com" class="hover:text-gray-900 dark:hover:text-gray-100 transition-colors">Niko / M.F.</a>
        </div>

        <div class="flex gap-4">
          <a href="https://hosting.nikostuff.com/legal/terms/" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Terms of Service</a>
          <a href="https://hosting.nikostuff.com/legal/privacy/" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Privacy Policy</a>
        </div>

        <div class="text-[10px] opacity-75 max-w-sm text-center sm:text-left leading-relaxed">
          NOT AN OFFICIAL MINECRAFT PRODUCT. NOT APPROVED BY OR ASSOCIATED WITH MOJANG OR MICROSOFT.
        </div>

        <div class="text-xs">
          &copy; {{ date('Y') }} · Powered by
          <a href="https://pelican.dev" target="_blank" class="font-medium hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Pelican</a>
        </div>
      </div>
    </div>
  </div>
</footer>