function processMobileTable(table) {
    if (!table || !table.classList.contains('fi-mobile-card-table')) return;

    const featuredColumn = table.getAttribute('data-featured-column');
    const featuredColor = table.getAttribute('data-featured-color');
    const badgeFieldsAttr = table.getAttribute('data-badge-fields') || '';
    const badgeColorsAttr = table.getAttribute('data-badge-colors') || '';

    const badgeFields = badgeFieldsAttr ? badgeFieldsAttr.split('|').filter(Boolean) : [];
    const badgeColors = {};
    if (badgeColorsAttr) {
        badgeColorsAttr.split('|').forEach(part => {
            const [key, value] = part.split(':');
            if (key && value) badgeColors[key.trim()] = value.trim();
        });
    }

    // Build header map: column-name -> header text
    const headerMap = {};
    table.querySelectorAll('.fi-ta-header-cell[class*="fi-ta-header-cell-"]').forEach(th => {
        const match = th.className.match(/fi-ta-header-cell-([\w-]+)/);
        if (match) {
            headerMap[match[1]] = th.textContent.trim();
        }
    });

    // Process data rows only
    const rows = table.querySelectorAll(
        '.fi-ta-row:not(.fi-ta-group-header-row):not(.fi-ta-summary-row):not(.fi-ta-row-not-reorderable)'
    );

    rows.forEach(row => {
        if (row.dataset.mobileProcessed === 'true') return;

        const badgeData = [];

        // Process data cells using Filament column-name classes
        row.querySelectorAll('.fi-ta-cell[class*="fi-ta-cell-"]').forEach(cell => {
            const match = cell.className.match(/fi-ta-cell-([\w-]+)/);
            if (!match) return;

            const columnName = match[1];
            const headerText = headerMap[columnName] || '';

            // Set label
            cell.setAttribute('data-label', headerText);

            // Check featured
            if (featuredColumn && headerText.toLowerCase() === featuredColumn.toLowerCase()) {
                cell.setAttribute('data-featured', 'true');
                if (featuredColor) {
                    cell.setAttribute('data-featured-color', featuredColor);
                }
            }

            // Check badges
            const isBadge = badgeFields.some(f => f.toLowerCase() === headerText.toLowerCase());
            if (isBadge) {
                cell.setAttribute('data-is-badge', 'true');
                const colorKey = Object.keys(badgeColors).find(
                    k => k.toLowerCase() === headerText.toLowerCase()
                ) || headerText;
                badgeData.push({
                    text: cell.textContent.trim(),
                    color: badgeColors[colorKey] || 'gray'
                });
            }
        });

        // Create badge container
        if (badgeData.length > 0) {
            let badgeContainer = row.querySelector('.fi-mobile-badges');
            if (!badgeContainer) {
                badgeContainer = document.createElement('div');
                badgeContainer.className = 'fi-mobile-badges';
                row.insertBefore(badgeContainer, row.firstChild);
            } else {
                badgeContainer.innerHTML = '';
            }

            badgeData.forEach(badge => {
                const span = document.createElement('span');
                span.className = 'fi-mobile-badge';
                span.textContent = badge.text;
                span.setAttribute('data-badge-color', badge.color);
                badgeContainer.appendChild(span);
            });
        }

        row.dataset.mobileProcessed = 'true';
    });

    table.dataset.mobileProcessed = 'true';
}

function processAllMobileTables() {
    document.querySelectorAll('.fi-mobile-card-table').forEach(processMobileTable);
}

function init() {
    // Initial processing
    processAllMobileTables();

    // Livewire 3 hooks - fires once per component after morph completes
    if (typeof Livewire !== 'undefined') {
        let pendingUpdate = false;

        Livewire.hook('morphed', ({ el }) => {
            if (pendingUpdate) return;
            pendingUpdate = true;

            requestAnimationFrame(() => {
                el.querySelectorAll('.fi-ta-row[data-mobile-processed]').forEach(row => {
                    delete row.dataset.mobileProcessed;
                });
                const table = el.closest('.fi-mobile-card-table') || el.querySelector('.fi-mobile-card-table');
                if (table) {
                    delete table.dataset.mobileProcessed;
                    processMobileTable(table);
                }
                pendingUpdate = false;
            });
        });

        document.addEventListener('livewire:navigated', () => {
            requestAnimationFrame(processAllMobileTables);
        });
    } else {
        let pendingUpdate = false;

        const observer = new MutationObserver((mutations) => {
            const relevant = mutations.some(m =>
                m.type === 'childList' &&
                (m.target.closest && m.target.closest('.fi-mobile-card-table'))
            );
            if (!relevant || pendingUpdate) return;

            pendingUpdate = true;
            requestAnimationFrame(() => {
                processAllMobileTables();
                pendingUpdate = false;
            });
        });

        const container = document.querySelector('.fi-ta-ctn') || document.body;
        observer.observe(container, { childList: true, subtree: true });
    }
}

// Ensure DOM is ready before processing
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
