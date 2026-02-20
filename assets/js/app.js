/**
 * Aqua B Water Refilling Station - Main JavaScript
 */
document.addEventListener('DOMContentLoaded', function () {

    /* ===== Auto-dismiss flash messages after 5 seconds ===== */
    document.querySelectorAll('.alert-dismissible').forEach(function (alert) {
        setTimeout(function () {
            alert.classList.add('alert-fade-out');
            alert.addEventListener('animationend', function () {
                alert.remove();
            });
        }, 5000);
    });

    /* ===== Delete confirmation dialog ===== */
    document.querySelectorAll('.delete-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!confirm('Are you sure you want to delete this record? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });

    /* ===== Prevent double-submit on forms ===== */
    document.querySelectorAll('form:not(.delete-form)').forEach(function (form) {
        form.addEventListener('submit', function () {
            var btn = form.querySelector('[type="submit"]');
            if (btn && !btn.disabled) {
                btn.disabled = true;
                setTimeout(function () { btn.disabled = false; }, 3000);
            }
        });
    });

    /* ===== Active nav link highlighting ===== */
    var currentPath = window.location.pathname;
    document.querySelectorAll('.navbar .nav-link').forEach(function (link) {
        if (link.getAttribute('href') && currentPath.indexOf(link.getAttribute('href')) !== -1) {
            link.classList.add('active');
        }
    });

    /* ===== Initialize Bootstrap tooltips ===== */
    var tooltipEls = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipEls.forEach(function (el) {
        new bootstrap.Tooltip(el);
    });
});

/* ===== Format number as Philippine Peso ===== */
function formatPeso(amount) {
    return '₱' + parseFloat(amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

/* ===== Print helper ===== */
function printSection(elementId) {
    var content = document.getElementById(elementId);
    if (!content) return;

    var printWindow = window.open('', '_blank');
    printWindow.document.write(
        '<html><head><title>Print</title>' +
        '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css">' +
        '<style>body{padding:20px;font-family:sans-serif}</style>' +
        '</head><body>' + content.innerHTML + '</body></html>'
    );
    printWindow.document.close();
    printWindow.onload = function () {
        printWindow.print();
        printWindow.close();
    };
}