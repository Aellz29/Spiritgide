document.addEventListener("DOMContentLoaded", () => {
  const menuBtn = document.querySelector("#menu-btn");
  const mobileMenu = document.querySelector("#mobile-menu");
  const navLinks = document.querySelectorAll("a[href^='#']");
  let isOpen = false;

  // 🔸 Toggle Hamburger Menu
  menuBtn.addEventListener("click", () => {
    isOpen = !isOpen;
    mobileMenu.classList.toggle("hidden");
    mobileMenu.classList.toggle("flex");
    mobileMenu.classList.toggle("animate-fade-slide");

    const bars = menuBtn.querySelectorAll("span");
    if (isOpen) {
      bars[0].classList.add("rotate-45", "translate-y-1.5");
      bars[1].classList.add("opacity-0");
      bars[2].classList.add("-rotate-45", "-translate-y-1.5");
    } else {
      bars[0].classList.remove("rotate-45", "translate-y-1.5");
      bars[1].classList.remove("opacity-0");
      bars[2].classList.remove("-rotate-45", "-translate-y-1.5");
    }
  });

  // 🔸 Smooth Scroll Navigation
  navLinks.forEach(link => {
    link.addEventListener("click", e => {
      e.preventDefault();
      const targetId = link.getAttribute("href").substring(1);
      const targetSection = document.getElementById(targetId);

      if (targetSection) {
        window.scrollTo({
          top: targetSection.offsetTop - 70, // offset biar gak ketutupan navbar
          behavior: "smooth"
        });
      }

      // Tutup menu setelah klik di mobile
      if (isOpen) {
        mobileMenu.classList.add("hidden");
        mobileMenu.classList.remove("flex");
        isOpen = false;
        const bars = menuBtn.querySelectorAll("span");
        bars[0].classList.remove("rotate-45", "translate-y-1.5");
        bars[1].classList.remove("opacity-0");
        bars[2].classList.remove("-rotate-45", "-translate-y-1.5");
      }
    });
  });
});
