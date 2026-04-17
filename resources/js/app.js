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

const revealMotionElement = (element) => {
    element.classList.add('is-visible');
};

const bootMotionSystem = () => {
    const elements = [...document.querySelectorAll('[data-motion]')];

    if (elements.length === 0) {
        return;
    }

    elements.forEach((element) => {
        if (element.dataset.motionDelay != null) {
            element.style.setProperty('--ops-motion-delay', `${element.dataset.motionDelay}ms`);
        }
    });

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        elements.forEach((element) => revealMotionElement(element));

        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            revealMotionElement(entry.target);
            observer.unobserve(entry.target);
        });
    }, {
        rootMargin: '0px 0px -10% 0px',
        threshold: 0.12,
    });

    elements.forEach((element) => {
        if (element.classList.contains('is-visible')) {
            return;
        }

        observer.observe(element);
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
    bootMotionSystem();
});

document.addEventListener('livewire:navigated', () => {
    bootAlertSystem();
    bootMotionSystem();
});
