(function truncateDescriptions() {
  const MAX_CHARS = 100;
  const selector = ".fi-ta-text-description, .hl-card-desc";

  function processElements() {
    const elements = document.querySelectorAll(selector);

    elements.forEach((el) => {
      if (el.dataset.truncated) return;

      const originalText = el.textContent.trim();

      if (originalText.length > MAX_CHARS) {
        el.title = originalText; // Tooltip on hover
        el.textContent = originalText.slice(0, MAX_CHARS).trim() + "…";
      }

      el.dataset.truncated = "true";
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", processElements);
  } else {
    processElements();
  }

  // Handles dynamic Livewire/Alpine DOM updates
  const observer = new MutationObserver(() => processElements());
  observer.observe(document.body, { childList: true, subtree: true });
})();
