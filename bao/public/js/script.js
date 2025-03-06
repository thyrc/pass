document.addEventListener('DOMContentLoaded', () => {
  (document.querySelectorAll('.notification .delete') || []).forEach(($delete) => {
    var $notification = $delete.parentNode;

    $delete.addEventListener('click', () => {
      localStorage.setItem($notification.id, "hide");
      $notification.parentNode.removeChild($notification);
    });
  });

  var $wrappingNote = document.getElementById("wrapping-notification");
  var $uploadNote = document.getElementById("upload-notification");
  var $shareNote = document.getElementById("share-notification");
  var $downloadNote = document.getElementById("download-notification");

  if ($wrappingNote) {
    if (localStorage.getItem("wrapping-notification") === "hide") {
      $wrappingNote.style.display = "none";
    };
  };

  if ($uploadNote) {
    if (localStorage.getItem("upload-notification") === "hide") {
      $uploadNote.style.display = "none";
    };
  };

  if ($shareNote) {
    if (localStorage.getItem("share-notification") === "hide") {
      $shareNote.style.display = "none";
    };
  };

  if ($downloadNote) {
    if (localStorage.getItem("download-notification") === "hide") {
      $downloadNote.style.display = "none";
    };
  };

  // Get all "navbar-burger" elements
  const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);

  // Check if there are any navbar burgers
  if ($navbarBurgers.length > 0) {

    // Add a click event on each of them
    $navbarBurgers.forEach( el => {
      el.addEventListener('click', () => {

        // Get the target from the "data-target" attribute
        const target = el.dataset.target;
        const $target = document.getElementById(target);

        // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
        el.classList.toggle('is-active');
        $target.classList.toggle('is-active');

      });
    });
  };

  if (document.getElementById('autosubmit') !== null) {
    document.getElementById('autosubmit').submit();
  };
});
