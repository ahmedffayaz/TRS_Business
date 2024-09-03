$(document).ready(function() {
    function switchLogo() {
        let $menuDiv = $('.main-menu');
        let $logoLight = $('.logo-light');
        let $logoDark = $('.logo-dark');
        let $lightShortLogo = $('.light-short-logo');
        let $darkShortLogo = $('.dark-short-logo');

        // Hide all logos initially
        $logoLight.hide();
        $logoDark.hide();
        $lightShortLogo.hide();
        $darkShortLogo.hide();

        // Determine which logos to show based on the menu's state
        if ($menuDiv.hasClass('expanded')) {
            if ($menuDiv.hasClass('menu-dark')) {
                $logoLight.hide();
                $lightShortLogo.hide();
                $logoDark.show();
            } else if ($menuDiv.hasClass('menu-light')) {
                $logoDark.hide();
                $lightShortLogo.hide();
                $logoLight.show();
            }
        } else {
            if ($menuDiv.hasClass('menu-dark')) {
                $darkShortLogo.show();
            } else if ($menuDiv.hasClass('menu-light')) {
                $lightShortLogo.show();
            }
        }
    }

    function cacheMenuState() {
        let $menuDiv = $('.main-menu');
        if ($menuDiv.hasClass('expanded')) {
            localStorage.setItem('menuState', 'expanded');
        } else {
            localStorage.setItem('menuState', 'collapsed');
        }
    }

    function applyCachedState() {
        let cachedState = localStorage.getItem('menuState');
        let $menuDiv = $('.main-menu');

        if (cachedState === 'expanded') {
            $menuDiv.addClass('expanded');
        } else {
            $menuDiv.removeClass('expanded');
        }
        switchLogo();
    }
    applyCachedState();

    switchLogo();

    const targetNode = document.querySelector('.main-menu');
    const config = {
        attributes: true,
        attributeFilter: ['class']
    };

    const callback = (mutationsList) => {
        for (const mutation of mutationsList) {
            if (mutation.type === 'attributes') {
                switchLogo();
                cacheMenuState();
            }
        }
    };

    const observer = new MutationObserver(callback);
    if (targetNode) {
        observer.observe(targetNode, config);
    }
});
