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
            searchInput.value = urlParams.get('search') || '';

            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const searchValue = this.value.trim();
                    const currentUrl = new URL(window.location.href);
                    
                    if (searchValue) {
                        // Remove any existing 'find' parameter if we're searching
                        currentUrl.searchParams.delete('find');
                        currentUrl.searchParams.set('search', searchValue);
                    } else {
                        currentUrl.searchParams.delete('search');
                    }
                    
                    // Reset to page 1 when searching
                    currentUrl.searchParams.delete('page');
                    
                    window.location.href = currentUrl.toString();
                }, 500);
            });
        }
    });
})(window, document);
