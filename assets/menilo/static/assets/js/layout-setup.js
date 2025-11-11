// layout setup

// Get HTML root element
const htmlElement = document.documentElement;

// List of all settings to check
const settings = [
  { attribute: "data-layout", defaultValue: "vertical" },
  { attribute: "data-bs-theme", defaultValue: "light" },
  { attribute: "data-content-width", defaultValue: "default" },
  { attribute: "dir", defaultValue: "rtl" },
  { attribute: "data-sidebar-color", defaultValue: "plum" },
  { attribute: "data-sidebar", defaultValue: "default" },
  { attribute: "data-theme-colors", defaultValue: "default" },
];

// Apply each setting from localStorage or use default
settings.forEach((setting) => {
  const savedValue = localStorage.getItem(setting.attribute);
  const valueToApply = savedValue || setting.defaultValue;

  // Apply to HTML element
  htmlElement.setAttribute(setting.attribute, valueToApply);
  if (setting.attribute === "dir") updateLayoutDir(valueToApply);
  else if (setting.attribute === "data-bs-theme") setTheme(valueToApply, false);

  const radioSelector = `input[name="${setting.attribute}"][value="${valueToApply}"]`;
  const radioElement = document.querySelector(radioSelector);
  if (radioElement) {
    radioElement.checked = true;
  }
});

updateSimpleBar(
  htmlElement.getAttribute("data-sidebar") ??
  htmlElement.getAttribute("data-layout")
);

if (document.documentElement.getAttribute("data-layout") === "horizontal") {
  removeHorizontalAttributes();
}

function updateMenuClick() {
  setTimeout(() => {
    // Get all menu links with the data-bs-toggle attribute
    const menuLinks = document.querySelectorAll('#sidebar-simplebar a.pe-nav-link[data-bs-toggle="collapse"]');

    // Add an event listener to each menu link
    menuLinks.forEach((link) => {
      link.addEventListener('click', (e) => {
        // Get the id of the collapse element
        const collapseId = link.getAttribute('href').replace('#', '');
        const parentCollapseId = link.closest('.collapse') ? link.closest('.collapse').id : null;
        // If the clicked link is already expanded, do nothing
        setTimeout(() => {
          if (parentCollapseId && document.getElementById(parentCollapseId).classList.contains('show')) {
            return;
          }
          // Close all open menus
          menuLinks.forEach((otherLink) => {
            const otherCollapseId = otherLink.getAttribute('href').replace('#', '');
            if (otherCollapseId !== collapseId) {
              const otherCollapseElement = document.getElementById(otherCollapseId);
              if (otherCollapseElement.classList.contains('show')) {
                otherCollapseElement.classList.remove('show');
                otherLink.setAttribute('aria-expanded', 'false');
              }
            }
          });
        }, 0);
      });
    });
  }, 500);
}

function updateSidebarClick() {
  const sideBarMenus = document.getElementById("sidebar-simplebar");
  if (sideBarMenus) {
    const menuItems = sideBarMenus.querySelectorAll("ul.pe-main-menu > li > a");
    menuItems.forEach((item) => {
      item.addEventListener("click", (e) => {
        e.preventDefault();
        if (document.documentElement.getAttribute("data-sidebar") === "icon") {
          setTimeout(() => {
            const activeMenus = sideBarMenus.querySelectorAll(
              "ul.pe-main-menu > li > ul.show"
            );
            activeMenus.forEach((activeItem) => {
              activeItem.classList.remove("show");
            });
            item.setAttribute("aria-expanded", "false");
            item.classList.remove("collapsed");
            item.nextElementSibling.classList.remove("show");
          }, 300);
        }
      });
    });
    updateMenuClick();
  }
}

function removeHorizontalAttributes() {
  document.documentElement.removeAttribute("data-sidebar");
  document.documentElement.setAttribute("data-topbar-theme", "dark");
}

function setTheme(valueToApply) {
  // Update corresponding radio button in the modal
  let THEME_MODE = "";
  if (valueToApply === "auto") {
    THEME_MODE = window.matchMedia("(prefers-color-scheme: dark)").matches
      ? "dark"
      : "light";
    htmlElement.setAttribute("data-bs-theme", THEME_MODE);
  } else {
    THEME_MODE = valueToApply;
  }
  
  // Set sidebar color based on theme and layout
  const layoutForTheme = document.documentElement.getAttribute("data-layout");
  if (layoutForTheme === "horizontal") {
    // For horizontal layout, keep existing logic
    if (THEME_MODE !== "light") {
      setAndSaveAttribute("data-sidebar-color", "dark");
    } else {
      setAndSaveAttribute("data-sidebar-color", "light");
    }
  } else {
    // For vertical layout, use theme-based colors
    if (THEME_MODE === "dark") {
      setAndSaveAttribute("data-sidebar-color", "dark");
    } else {
      setAndSaveAttribute("data-sidebar-color", "plum");
    }
  }
}

// Function to set an attribute and save to localStorage
function setAndSaveAttribute(attributeName, value) {
  htmlElement.setAttribute(attributeName, value);
  localStorage.setItem(attributeName, value);
  // Update the corresponding radio button in the modal
  const radioSelector = `input[name="${attributeName}"][value="${value}"]`;
  const radioElement = document.querySelector(radioSelector);
  if (radioElement) {
    radioElement.checked = true;
  }
}

function updateLayoutDir(value) {
  const bootstrapCss = document.getElementById("bootstrap-style");
  const appCss = document.getElementById("app-style");
  if (bootstrapCss === null || appCss === null) return;
  if (value === "rtl") {
    bootstrapCss.href = bootstrapCss.href.replace(
      "bootstrap.min.css",
      "bootstrap-rtl.min.css"
    );
    appCss.href = appCss.href.replace("app.min.css", "app-rtl.min.css");
  } else {
    bootstrapCss.href = bootstrapCss.href.replace(
      "bootstrap-rtl.min.css",
      "bootstrap.min.css"
    );
    appCss.href = appCss.href.replace("app-rtl.min.css", "app.min.css");
  }
}

function updateSimpleBar(value) {
  const sidebarSimpleBarMenu = document.getElementById("sidebar-simplebar");
  if (
    (value === "vertical" ||
      value === "horizontal" ||
      value === "default" ||
      value === "semibox" ||
      value === "medium" ||
      value === "icon-hover") &&
    document.documentElement.getAttribute("data-sidebar") !== "icon"
  ) {
    if (sidebarSimpleBarMenu) {
      const allMenus = sidebarSimpleBarMenu.querySelector("ul.pe-main-menu");
      if (allMenus) {
        sidebarSimpleBarMenu.innerHTML = allMenus.parentElement.innerHTML;
      }
      // Add 'simplebar' class to initialize it
      sidebarSimpleBarMenu.setAttribute("data-simplebar", "");

      // Initialize SimpleBar for custom scrolling
      if (window.SimpleBar) {
        new SimpleBar(sidebarSimpleBarMenu); // Initialize a new SimpleBar instance
        updateSidebarClick();
      }
    }
  } else {
    setTimeout(() => {
      if (window.SimpleBar) {
        const simpleBarInstance = SimpleBar.instances.get(sidebarSimpleBarMenu);
        if (simpleBarInstance) {
          simpleBarInstance.unMount(); // Unmount and remove the instance
          const allMenus =
            sidebarSimpleBarMenu.querySelector("ul.pe-main-menu");
          if (allMenus) {
            sidebarSimpleBarMenu.innerHTML = allMenus.parentElement.innerHTML;
            updateSidebarClick();
          }
        }
      }
    }, 500);
  }
}

// Function to handle radio button selection
function handleRadioChange(event) {
  const { name, value } = event.target;

  // Set attribute based on option group
  switch (name) {
    case "data-bs-theme":
      setAndSaveAttribute("data-bs-theme", value);
      setTheme(value);
      // Force sidebar color update based on new theme
      const themeLayout = document.documentElement.getAttribute("data-layout");
      if (value === "dark") {
        setAndSaveAttribute("data-sidebar-color", "dark");
      } else if (themeLayout === "horizontal") {
        setAndSaveAttribute("data-sidebar-color", "light");
      } else {
        setAndSaveAttribute("data-sidebar-color", "plum");
      }
      break;
    case "data-layout":
      setAndSaveAttribute("data-layout", value);
      if (value === "horizontal") {
        removeHorizontalAttributes();
        setAndSaveAttribute("data-topbar-theme", "dark");
        // Set sidebar color based on current theme for horizontal layout
        const horizontalTheme = document.documentElement.getAttribute("data-bs-theme");
        if (horizontalTheme === "dark") {
          setAndSaveAttribute("data-sidebar-color", "dark");
        } else {
          setAndSaveAttribute("data-sidebar-color", "light");
        }
      } else {
        // When switching back to vertical layout
        document.documentElement.removeAttribute("data-topbar-theme");
        // Set sidebar color based on current theme for vertical layout
        const verticalTheme = document.documentElement.getAttribute("data-bs-theme");
        if (verticalTheme === "dark") {
          setAndSaveAttribute("data-sidebar-color", "dark");
        } else {
          setAndSaveAttribute("data-sidebar-color", "plum");
        }
      }
      updateSimpleBar(value);
      break;
    case "data-content-width":
      setAndSaveAttribute("data-content-width", value);
      break;
    case "dir":
      setAndSaveAttribute("dir", value);
      updateLayoutDir(value);
      break;
    case "data-sidebar":
      if (document.documentElement.getAttribute("data-layout") !== "horizontal")
        setAndSaveAttribute("data-sidebar", value);
      updateSimpleBar(value);
      break;
    case "data-sidebar-color":
      // Only allow manual color selection in vertical layout with light theme
      const sidebarLayout = document.documentElement.getAttribute("data-layout");
      const sidebarTheme = document.documentElement.getAttribute("data-bs-theme");
      
      if (sidebarLayout !== "horizontal" && sidebarTheme === "light") {
        setAndSaveAttribute("data-sidebar-color", value);
      } else {
        // For other cases, force theme-based colors
        if (sidebarTheme === "dark") {
          setAndSaveAttribute("data-sidebar-color", "dark");
        } else if (sidebarLayout === "horizontal") {
          setAndSaveAttribute("data-sidebar-color", "light");
        } else {
          setAndSaveAttribute("data-sidebar-color", "plum");
        }
      }
      break;
    case "data-theme-colors":
      setAndSaveAttribute("data-theme-colors", value);
      break;
    default:
      break;
  }
}

// Attach event listeners to all radio buttons in the customizer
document
  .querySelectorAll('.layout-customizer input[type="radio"]')
  .forEach((radio) => {
    radio.addEventListener("change", handleRadioChange);
  });

// dark light mode toggle
const toggleMode = document.getElementById("toggleMode");
const lightModeButton = document.getElementById("lightModeBtn");
toggleMode?.addEventListener("click", () => {
  const currentTheme = htmlElement.getAttribute("data-bs-theme");
  const newTheme = currentTheme === "light" ? "dark" : "light";
  setAndSaveAttribute("data-bs-theme", newTheme);
  setTheme(newTheme);
  // The setTheme function now handles sidebar color automatically
});

// vertical toggle button
const toggleButton = document.getElementById("toggleSidebar");
const sideBarBackdrop = document.getElementById("sidebar-backdrop");
sideBarBackdrop?.addEventListener("click", () => {
  if (htmlElement.getAttribute("data-layout") === "horizontal") {
    const horizontalAside = document.getElementById("horizontal-aside");
    horizontalAside.classList.toggle("show");
  } else {
    const sidebar = document.getElementById("sidebar");
    sidebar.classList.remove("show");
  }
});

function removeShowClassFromSidebar() {
  const sidebarSimpleBarMenu = document.getElementById("sidebar-simplebar");
  sidebarSimpleBarMenu
    ?.querySelectorAll(".pe-slide-menu.collapse.show")
    .forEach((element) => {
      element.classList.remove("show");
    });
}

toggleButton?.addEventListener("click", () => {
  const currentToggled = htmlElement.getAttribute("data-sidebar");
  if (window.innerWidth < 1024) {
    // Toggle the data-vertical-layout value
    const sidebar = document.getElementById("sidebar");
    sidebar.classList.add("show");
    if (document.documentElement.getAttribute("data-layout") === "horizontal")
      removeHorizontalAttributes();
  } else {
    // Toggle the data-vertical-layout value
    if (currentToggled === "icon") {
      htmlElement.setAttribute("data-sidebar", "default");
    } else {
      htmlElement.setAttribute("data-sidebar", "icon");
      removeShowClassFromSidebar();
    }
  }
  updateSimpleBar(htmlElement.getAttribute("data-layout"));
});

// horizontal toggle button
const horizontalToggle = document.getElementById("toggleHorizontal");
horizontalToggle?.addEventListener("click", () => {
  if (window.innerWidth < 1200) {
    // Toggle the data-vertical-layout value
    const horizontalAside = document.getElementById("horizontal-aside");
    horizontalAside.classList.toggle("show");
  }
});

// Function to handle sidebar menu active links
let currentUrl = window.location.pathname; // Get the current URL
const menuLinks = document.querySelectorAll(
  "#sidebar .pe-main-menu .pe-nav-link"
); // Select all menu links
if (currentUrl === "/") currentUrl = "/index.html";
const currentLayoutGlobal = htmlElement.getAttribute("data-layout");
const currentSidebar = htmlElement.getAttribute("data-sidebar");

menuLinks.forEach((link) => {
  const linkHref = link.getAttribute("href"); // Get the href attribute of the link

  // Check if the current URL contains the link's href
  if (currentUrl.includes(linkHref)) {
    link.classList.add("active"); // Add active class to the link

    // Function to open all parent dropdowns
    const openParentDropdowns = (element) => {
      let parentDropdown = element.closest(".pe-has-sub");
      while (parentDropdown) {
        const collapseId = parentDropdown
          .querySelector(".pe-nav-link")
          .getAttribute("aria-controls");
        const collapseElement = document.getElementById(collapseId);
        if (collapseElement) {
          parentDropdown.children[0].classList.add("active"); // Add active class to the parent link
          if (currentLayoutGlobal !== "horizontal" && currentSidebar !== "icon") {
            collapseElement.classList.add("show"); // Open the dropdown
            parentDropdown.children[0].setAttribute("aria-expanded", "true"); // Add active class to the parent link
            parentDropdown.querySelector(".pe-nav-arrow").classList.add("open"); // Add open class to the arrow
          }
        }
        parentDropdown = parentDropdown.parentElement.closest(".pe-has-sub"); // Move to the next parent dropdown
      }
    };
    // Open all parent dropdowns for the active link
    openParentDropdowns(link);
  }
});

// active horizontal menu
const horizontalMenuLinks = document.querySelectorAll(
  "#horizontal-menu .pe-nav-link"
);

horizontalMenuLinks.forEach((link) => {
  const linkHref = link.getAttribute("href"); // Get the href attribute of the link
  if (currentUrl.includes(linkHref)) {
    link.classList.add("active");
    const parentDropdown = link.closest(".pe-has-sub");
    if (parentDropdown) {
      const collapseId = parentDropdown
        .querySelector(".pe-nav-link")
        .getAttribute("aria-controls");
      const collapseElement = document.getElementById(collapseId);
      if (collapseElement) {
        parentDropdown.children[0].classList.add("active"); // Add active class to the parent link
        const parentParentDropdown =
          parentDropdown.parentElement.closest(".pe-has-sub");
        if (parentParentDropdown) {
          const parentCollapseId = parentParentDropdown
            .querySelector(".pe-nav-link")
            .getAttribute("aria-controls");
          const parentCollapseElement =
            document.getElementById(parentCollapseId);
          if (parentCollapseElement) {
            parentParentDropdown.children[0].classList.add("active"); // Add active class to the parent link
          }
        }
      }
    }
  }
});

// rest layout to default
const resetBtn = document.getElementById("resetBtn"); // Get the resetBtn

resetBtn?.addEventListener("click", () => {
  localStorage.clear(); // Clear localStorage
  window.location.reload(); // Reload the page
});

// icon sidebar to default
const sidebarDefaultArrow = document.getElementById("sidebarDefaultArrow");

sidebarDefaultArrow?.addEventListener("click", () => {
  setAndSaveAttribute("data-sidebar", "default");
  document.querySelector('input[name="data-sidebar"]').checked = true;
});

// Run on page load
handleResponsiveSidebar();

function handleResponsiveSidebar() {
  if (window.innerWidth < 992) {
    document.documentElement.removeAttribute("data-sidebar");
  }
}

// Run on window resize
window.addEventListener("resize", handleResponsiveSidebar);