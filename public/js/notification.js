const notifications = document.querySelector(".notificationsToasts");

const toastDetails = {
  success: {
    icon: "ph ph-checks",
  },
  error: {
    icon: "ph ph-x",
  },
  warning: {
    icon: "ph ph-warning",
  },
  info: {
    icon: "ph ph-info",
  },
};

const notificationRemoveToast = (toast) => {
  toast.classList.add("hide");
  if (toast.timeoutId) clearTimeout(toast.timeoutId);
  setTimeout(() => toast.remove(), 500);
};

const notificationsToast = (type, message, local = "top-left", time = 5000) => {
  const { icon } = toastDetails[type];
  const text = message;
  const toast = document.createElement("li");
  toast.className = `zarkify-toast ${type}`;

  toast.innerHTML = `<div class="column">
    <i class="fa-solid ${icon}"></i>
    <span>${text}</span>
    </div>
    <i class="ms-3 fa-solid fa-xmark" onclick="notificationRemoveToast(this.parentElement)"></i>`;
  notifications.appendChild(toast);

  toast.timeoutId = setTimeout(() => notificationRemoveToast(toast), time);
};
