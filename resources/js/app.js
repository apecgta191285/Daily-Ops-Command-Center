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

const bootStaggerGroups = () => {
    document.querySelectorAll('[data-motion-group]').forEach((group) => {
        const baseDelay = Number.parseInt(group.dataset.staggerBase ?? '40', 10);
        const stepDelay = Number.parseInt(group.dataset.staggerUnit ?? '30', 10);
        const maxDelay = Number.parseInt(group.dataset.staggerMax ?? '400', 10);

        group.querySelectorAll('[data-motion]').forEach((item, index) => {
            if (item.dataset.motionDelay != null) {
                item.style.setProperty('--ops-motion-delay', `${item.dataset.motionDelay}ms`);

                return;
            }

            const computedDelay = Math.min(baseDelay + (index * stepDelay), maxDelay);
            item.style.setProperty('--ops-motion-delay', `${computedDelay}ms`);
        });
    });
};

const bootMeterAnimation = () => {
    const meters = [...document.querySelectorAll('[data-meter-target]')];

    if (meters.length === 0) {
        return;
    }

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        meters.forEach((meter) => {
            meter.style.width = `${meter.dataset.meterTarget ?? '0'}%`;
        });

        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            const meter = entry.target;
            const target = Number.parseInt(meter.dataset.meterTarget ?? '0', 10);

            window.setTimeout(() => {
                meter.style.width = `${Math.max(0, Math.min(target, 100))}%`;
            }, 120);

            observer.unobserve(meter);
        });
    }, {
        threshold: 0.45,
    });

    meters.forEach((meter) => {
        if (meter.dataset.meterBound === 'true') {
            return;
        }

        meter.dataset.meterBound = 'true';
        meter.style.width = '0%';
        observer.observe(meter);
    });
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
    bootStaggerGroups();
    bootMotionSystem();
    bootMeterAnimation();
});

document.addEventListener('livewire:navigated', () => {
    bootAlertSystem();
    bootStaggerGroups();
    bootMotionSystem();
    bootMeterAnimation();
});
