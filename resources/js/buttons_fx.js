document.addEventListener("mousemove", (e) => {
  const target = e.target.closest("button, a, [data-glow], .glow-card");
  if (!target) return;

  if (!target.hasAttribute("data-glow-initialized")) {
    const computedStyle = window.getComputedStyle(target);
    const borderRadius = computedStyle.borderRadius ? computedStyle.borderRadius.trim() : "";

    if (!borderRadius || borderRadius === "0px") return;

    target.setAttribute("data-glow-initialized", "true");

    if (window.getComputedStyle(target).position === "static") {
      target.style.position = "relative";
    }

    Array.from(target.children).forEach((child) => {
      if (!child.classList.contains("border-glow-layer")) {
        child.style.pointerEvents = "none";
      }
    });

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
      zIndex: "1",
      transition: "opacity 0.3s ease",
      background:
        "radial-gradient(280px circle at var(--x, 0px) var(--y, 0px), rgba(255, 255, 255, 0.35) 0%, rgba(255, 255, 255, 0.02) 60%, transparent 100%)",
      webkitMask:
        "linear-gradient(#fff, #fff) content-box, linear-gradient(#fff, #fff)",
      webkitMaskComposite: "xor",
      maskComposite: "exclude",
    });

    target.appendChild(borderGlow);

    target.addEventListener("mousemove", (elEvent) => {
      if (elEvent.currentTarget === target) {
        borderGlow.style.setProperty("--x", `${elEvent.offsetX}px`);
        borderGlow.style.setProperty("--y", `${elEvent.offsetY}px`);
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