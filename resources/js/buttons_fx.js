document.addEventListener("mousemove", (e) => {
  const selector = "button, a, [data-glow], .glow-card, .fi-input-wrp, .fi-badge, .fi-dropdown-panel, input, select, textarea";
  let target = e.target.closest(selector);
  if (!target) return;

  if (["INPUT", "SELECT", "TEXTAREA"].includes(target.tagName)) {
    target = target.closest(".fi-input-wrp") || target.parentElement;
    if (!target) return;
  }

  if (!target.hasAttribute("data-glow-initialized")) {
    const computedStyle = window.getComputedStyle(target);
    const borderRadius = computedStyle.borderRadius ? computedStyle.borderRadius.trim() : "";

    if (!borderRadius || borderRadius === "0px") return;

    target.setAttribute("data-glow-initialized", "true");

    if (window.getComputedStyle(target).position === "static") {
      target.style.position = "relative";
    }

    const borderGlow = document.createElement("span");
    borderGlow.classList.add("border-glow-layer");
    const offsetSize = "1px";

    Object.assign(borderGlow.style, {
      position: "absolute",
      top: `-${offsetSize}`,
      left: `-${offsetSize}`,
      right: `-${offsetSize}`,
      bottom: `-${offsetSize}`,
      padding: "1px",
      borderRadius: `calc(${borderRadius} + ${offsetSize})`,
      pointerEvents: "none",
      opacity: "0",
      zIndex: "5",
      transition: "opacity 0.3s ease",
      background:
        "radial-gradient(280px circle at var(--x, 0px) var(--y, 0px), rgba(255, 255, 255, 0.4) 0%, rgba(255, 255, 255, 0.05) 60%, transparent 100%)",
      webkitMask:
        "linear-gradient(#fff, #fff) content-box, linear-gradient(#fff, #fff)",
      webkitMaskComposite: "xor",
      maskComposite: "exclude",
    });

    target.appendChild(borderGlow);

    target.addEventListener("mousemove", (elEvent) => {
      if (elEvent.currentTarget === target) {
        const rect = target.getBoundingClientRect();
        const x = elEvent.clientX - rect.left;
        const y = elEvent.clientY - rect.top;
        
        borderGlow.style.setProperty("--x", `${x}px`);
        borderGlow.style.setProperty("--y", `${y}px`);
        borderGlow.style.opacity = "1";
      }
    });

    target.addEventListener("mouseleave", () => {
      borderGlow.style.opacity = "0";
    });
  }

  if (!target.matches(":hover")) {
    const borderGlow = target.querySelector(".border-glow-layer");
    if (borderGlow) {
      borderGlow.style.opacity = "0";
    }
  }
});
