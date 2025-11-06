// DARK MODE TOGGLE
const toggle = document.getElementById("theme-toggle");
if (toggle) {
  const current = localStorage.getItem("theme");
  if (current === "dark") {
    document.body.classList.add("dark");
    toggle.innerHTML = '<i class="fa-solid fa-sun"></i>';
  }

  toggle.addEventListener("click", () => {
    document.body.classList.toggle("dark");
    const isDark = document.body.classList.contains("dark");
    toggle.innerHTML = isDark
      ? '<i class="fa-solid fa-sun"></i>'
      : '<i class="fa-solid fa-moon"></i>';
    localStorage.setItem("theme", isDark ? "dark" : "light");
  });
}

// PHONE FORMATTER
const phoneInput = document.getElementById("phone");
if (phoneInput) {
  phoneInput.addEventListener("input", () => {
    let digits = phoneInput.value.replace(/\D/g, "");
    if (digits.length > 9) digits = digits.slice(0, 9);
    const parts = [];
    for (let i = 0; i < digits.length; i += 3)
      parts.push(digits.substring(i, i + 3));
    phoneInput.value = parts.join(" ");
  });

  phoneInput.addEventListener("keypress", (e) => {
    if (!/[0-9]/.test(e.key)) e.preventDefault();
  });

  if (document.querySelector("form")) {
    document.querySelector("form").addEventListener("submit", (e) => {
      const digits = phoneInput.value.replace(/\D/g, "");
      if (digits.length !== 9) {
        e.preventDefault();
        alert("Numer telefonu musi mieć dokładnie 9 cyfr!");
      }
    });
  }
}
