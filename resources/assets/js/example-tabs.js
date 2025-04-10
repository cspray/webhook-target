(() => {
    document.addEventListener('DOMContentLoaded', () => {
        const tabButtons = document.querySelectorAll('.feature-hero .tabs li');
        const tabContents = document.querySelectorAll('.feature-hero .tabs-content .tab-content');

        tabButtons.forEach((tabButton) => {
            const tabButtonLink = tabButton.querySelector('a');
            tabButtonLink.addEventListener('click', (clickEvent) => {
                clickEvent.preventDefault();

                const group = tabButtonLink.attributes.getNamedItem('data-group').value;
                const target = tabButtonLink.attributes.getNamedItem('data-target').value;

                tabButtons.forEach((tb) => {
                    const tbLink = tb.querySelector('a');
                    const tbGroup = tbLink.attributes.getNamedItem('data-group').value;
                    const tbTarget = tbLink.attributes.getNamedItem('data-target').value;

                    if (tbGroup !== group) {
                        return;
                    }

                    if (tbTarget === target) {
                        if (!tb.classList.contains('is-active')) {
                            tb.classList.add('is-active')
                        }
                    } else {
                        tb.classList.remove('is-active');
                    }
                });

                tabContents.forEach((tabContent) => {
                    const tabGroup = tabContent.attributes.getNamedItem('data-group').value;
                    const tabId = tabContent.id;

                    if (tabGroup !== group) {
                        return;
                    }

                    if (tabId === target) {
                        tabContent.classList.remove('is-hidden');
                    } else {
                        if (!tabContent.classList.contains('is-hidden')) {
                            tabContent.classList.add('is-hidden');
                        }
                    }
                });
            });
        });
    });
})();
