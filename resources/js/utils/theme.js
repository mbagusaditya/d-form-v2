export function initTheme() {
    let theme = getTheme();

    document.documentElement.setAttribute('data-theme', theme);
}

export function setTheme(theme) {
    if (!['light', 'dark'].includes(theme)) {
        theme = 'light';
    }

    localStorage.setItem('theme', theme);

    document.documentElement.setAttribute('data-theme', theme);
}

export function getTheme() {
    let theme = localStorage.getItem('theme');

    if (!theme || !['light', 'dark'].includes(theme)) {
        theme = setTheme('light');
    }

    return theme;
}
