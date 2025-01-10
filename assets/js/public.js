/**
 * Kostildemo project.
 *
 * Purpose: performs a frontend logic for job running and admin features.
 */

(function(window, document)
{
    document.addEventListener('DOMContentLoaded', function ()
    {
        const PublicUrl = (function()
        {
            let data = document.documentElement.dataset.publicUrl;
            let url = new URL(data);

            return url.origin + url.pathname;
        })();

        // Initialize Job runner (if required).
        (function()
        {
            const jobKeyElement = document.querySelector('meta[name=job_key]');
            if (!jobKeyElement)
            {
                return;
            }

            fetch(PublicUrl + '?controller=demo&action=cleanup&hash=' + jobKeyElement.content)
                .then(response => response.json())
                .then(data => {
                    if (!data || !data.entries || !data.entries.length)
                    {
                        return;
                    }

                    data.entries.forEach(demo => document.querySelector('[data-demo="' + demo + '"]').remove());
                })
                .catch(error => console.error('Error when deleting a demo records:', error));
        })();

        // Initialize delete buttons.
        (function ()
        {
            const onDeleteHandler = function (ev)
            {
                const button = ev.currentTarget;
                const recordElement = button.closest('[data-demo]');

                fetch(PublicURL + '?controller=demo&action=delete&id=' + recordElement.dataset.demoId)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success)
                        {
                            return;
                        }

                        recordElement.remove();
                    });
            };

            const deleteButtons = document.querySelectorAll('.js-deleteDemoRecord');
            for (const deleteButton of deleteButtons)
            {
                deleteButton.addEventListener('click', onDeleteHandler);
            }
        })();

        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            // Set initial value from URL params
            const urlParams = new URLSearchParams(window.location.search);
            const findParam = urlParams.get('find');
            searchInput.value = urlParams.get('search') || findParam || '';

            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const searchValue = this.value.trim();
                    const currentUrl = new URL(window.location.href);
                    
                    if (searchValue) {
                        // Check if search value is a numeric player ID
                        if (/^\d+$/.test(searchValue)) {
                            currentUrl.searchParams.delete('search');
                            currentUrl.searchParams.set('find', searchValue);
                        } else {
                            currentUrl.searchParams.delete('find');
                            currentUrl.searchParams.set('search', searchValue);
                        }
                    } else {
                        currentUrl.searchParams.delete('search');
                        currentUrl.searchParams.delete('find');
                    }
                    
                    // Reset to page 1 when searching
                    currentUrl.searchParams.delete('page');
                    
                    window.location.href = currentUrl.toString();
                }, 500);
            });
        }

        // Theme handling
        const initializeTheme = () => {
            const themeToggle = document.getElementById('themeToggle');
            const themeIcon = document.getElementById('themeIcon');
            
            // Check for saved theme preference or default to light
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            updateThemeIcon(savedTheme);
            
            themeToggle.addEventListener('click', () => {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                
                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateThemeIcon(newTheme);
            });
            
            function updateThemeIcon(theme) {
                themeIcon.setAttribute('name', theme === 'light' ? 'moon-outline' : 'sunny-outline');
            }
        };

        initializeTheme();

        // Per page selector handling
        const perPageSelect = document.getElementById('perPageSelect');
        if (perPageSelect) {
            const urlParams = new URLSearchParams(window.location.search);
            perPageSelect.value = urlParams.get('per_page') || '10';

            perPageSelect.addEventListener('change', function() {
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('per_page', this.value);
                currentUrl.searchParams.delete('page'); // Reset to page 1
                window.location.href = currentUrl.toString();
            });
        }
    });
})(window, document);
