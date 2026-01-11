(function() {
  var _a;
  const csrfToken = (_a = document.querySelector('meta[name="csrf-token"]')) == null ? void 0 : _a.getAttribute("content");
  async function sendTrackingData(endpoint, data) {
    if (!csrfToken) {
      console.warn("AuditTrail: CSRF token not found");
      return;
    }
    try {
      const response = await fetch(endpoint, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": csrfToken,
          "Accept": "application/json"
        },
        body: JSON.stringify(data)
      });
      if (!response.ok) {
        console.warn("AuditTrail: Failed to send tracking data", response.status);
      }
    } catch (error) {
      console.warn("AuditTrail: Error sending tracking data", error);
    }
  }
  function trackButtonClick(event) {
    const target = event.target.closest("[data-audit-track]");
    if (!target) return;
    const buttonId = target.dataset.auditTrack;
    const actionName = target.dataset.auditAction || target.textContent.trim() || buttonId;
    sendTrackingData("/pengurusan/audit-trail/click", {
      button_id: buttonId,
      action_name: actionName,
      url: window.location.href,
      route_name: document.body.dataset.routeName || null
    });
  }
  function trackFormSubmit(event) {
    const form = event.target;
    if (!form.dataset.auditForm) return;
    const formName = form.dataset.auditForm;
    sendTrackingData("/pengurusan/audit-trail/form-submit", {
      form_name: formName,
      success: true,
      // Will be updated by server response
      url: window.location.href
    });
  }
  function init() {
    document.addEventListener("click", trackButtonClick, true);
    document.addEventListener("submit", trackFormSubmit, true);
    console.log("AuditTrail: Tracker initialized");
  }
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
