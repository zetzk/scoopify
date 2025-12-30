const APP_URL = `${window.location.protocol}//${window.location.host}`;


const basePath =
  "/" +
  window.location.pathname
    .split("/") 
    .filter(Boolean) 
    .slice(0, 3) 
    .join("/") +
  "/";

function route(complement) {
  return basePath + complement;
}

/**
 * Responsible for obtaining Query String parameters
 * @returns {object} - Params of url
 */
function getQuery() {
  var params = {};
  var queryString = window.location.search.substring(1);

  if (queryString) {
    var queries = queryString.split("&");
    queries.forEach(function (query) {
      var parts = query.split("=");
      params[decodeURIComponent(parts[0])] = decodeURIComponent(parts[1] || "");
    });
  }
  return params;
}

/**
 * Responsible for obtaining an element by ID
 * @param {String} id
 * @returns object - Params of url
 */
function getElement(inputId) {
  let input = document.getElementById(inputId);
  if (input) return input;
  return false;
}

/**
 * This method aims to set the value of a field through the ID
 * @param {String} id
 * @param {*} value
 * @return {true|false}
 */
function setValueById(id, value) {
  if ((element = document.getElementById(id))) {
    element.value = value;
    return true;
  }
  return false;
}

async function getPage(url) {
  try {
    const response = await fetch(url);
    if (!response.ok) {
      throw new Error("Network response was not ok");
    }
    const data = await response.text();
    return data;
  } catch (error) {
    console.error("Houve um problema ao tentar buscar o arquivo:", error);
    return null;
  }
}

const tooltipTriggerList = document.querySelectorAll(
  '[data-bs-toggle="tooltip"]'
);
const tooltipList = [...tooltipTriggerList].map(
  (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
);

function cloneElement(elm, options) {
  const newBlock = elm.cloneNode(true);
  if (options.input)
    newBlock.querySelectorAll("input").forEach((input) => (input.value = ""));
  if (options.small)
    newBlock
      .querySelectorAll("small")
      .forEach((small) => (small.innerText = ""));

  return newBlock;
}

function loading(state, ignoreForm) {
  if (ignoreForm) {
    state === "show"
      ? (document.getElementById("loading-wrapper").style.display = "flex")
      : (document.getElementById("loading-wrapper").style.display = "none");
    return;
  }
  if (validateForm("formMenu")) {
    if (state === "show") {
      document.getElementById("loading-wrapper").style.display = "flex";
    } else {
      document.getElementById("loading-wrapper").style.display = "none";
    }
  }
}

function validateForm(formId) {
  const form = document.getElementById(formId);
  if (!form) {
    console.error("Form not found.");
    return false;
  }

  const fields = form.querySelectorAll(
    "input[required], select[required], textarea[required]"
  );

  let isValid = true;

  fields.forEach((f) => {
    if (!f.value.trim()) {
      f.classList.add("is-invalid");
      isValid = false;
    } else {
      f.classList.remove("is-invalid");
    }
  });

  return isValid;
}
