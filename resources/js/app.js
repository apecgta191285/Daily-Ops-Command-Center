const dismissAlert = (alert) => {
    if (!alert || alert.dataset.dismissing === 'true') {
        return;
    }

    alert.dataset.dismissing = 'true';
    alert.classList.add('is-dismissing');

    window.setTimeout(() => {
        alert.remove();
    }, 220);
};

const bootAlertSystem = () => {
    document.querySelectorAll('[data-alert][data-auto-dismiss]').forEach((alert) => {
        if (alert.dataset.autoDismissBound === 'true') {
            return;
        }

        alert.dataset.autoDismissBound = 'true';

        const delay = Number.parseInt(alert.dataset.autoDismiss ?? '0', 10);

        if (Number.isFinite(delay) && delay > 0) {
            window.setTimeout(() => dismissAlert(alert), delay);
        }
    });
};

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-dismiss-alert]');

    if (!button) {
        return;
    }

    const alert = button.closest('[data-alert]');

    dismissAlert(alert);
});

document.addEventListener('DOMContentLoaded', () => {
    document.documentElement.dataset.js = 'ready';
    bootAlertSystem();
});

document.addEventListener('livewire:navigated', () => {
    bootAlertSystem();
});
