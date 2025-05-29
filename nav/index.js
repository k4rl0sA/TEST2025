const navToggle = document.querySelector(".nav-toggle");
const navMenu = document.querySelector(".nav-menu");

navToggle.addEventListener("click", () => {
  navMenu.classList.toggle("nav-menu_visible");

  if (navMenu.classList.contains("nav-menu_visible")) {
    navToggle.setAttribute("aria-label", "Cerrar menú");
  } else {
    navToggle.setAttribute("aria-label", "Abrir menú");
  }
});



const all = document.querySelector('body');
const thm = document.getElementById('theme');

if (localStorage.getItem('demo-theme')) {
  const theme = localStorage.getItem('demo-theme');
  all.classList.add(`theme-${theme}`);
}

  thm.addEventListener('change', e => {
    let colour = thm.value;
    all.className = '';
    all.classList.add(`theme-${colour}`);
    localStorage.setItem('demo-theme', colour);
  });

