document.addEventListener('DOMContentLoaded', () => {
    // Fetch and display dashboard stats
    fetch('system_admin_api.php?view=dashboard_stats')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.stats) {
                const stats = data.stats;
                document.getElementById('total-registered').textContent = stats.total_registered;
                document.getElementById('total-pending').textContent = stats.pending;
                document.getElementById('total-approved').textContent = stats.approved;
                document.getElementById('total-rejected').textContent = stats.rejected;
                document.getElementById('total-suspended').textContent = stats.suspended;

                const grid = document.getElementById('role-stats-grid');
                grid.innerHTML = ''; // Clear existing
                for (const roleName in stats.roles) {
                    const role = stats.roles[roleName];
                    const card = `
                        <div class="role-card">
                            <div class="role-card__top">
                                <h4>${escapeHTML(roleName)}</h4>
                                <span class="pill">${role.total} total</span>
                            </div>
                            <div class="role-card__row"><span class="dot pending"></span> Pending: ${role.pending}</div>
                            <div class="role-card__row"><span class="dot approved"></span> Approved: ${role.approved}</div>
                            <div class="role-card__row"><span class="dot rejected"></span> Rejected: ${role.rejected}</div>
                            <div class="role-card__row"><span class="dot suspended"></span> Suspended: ${role.suspended}</div>
                        </div>
                    `;
                    grid.innerHTML += card;
                }
            }
        })
        .catch(error => console.error('Error fetching dashboard stats:', error));

    function filterTable(tableId, searchInput, roleFilter = null, statusFilter = null) {
        const rows = document.querySelectorAll(`#${tableId} tbody tr`);
        const searchVal = (searchInput.value || '').toLowerCase();
        const roleVal = roleFilter ? roleFilter.value.toLowerCase() : '';
        const statusVal = statusFilter ? statusFilter.value.toLowerCase() : '';

        rows.forEach(row => {
            const name = row.children[1]?.textContent.toLowerCase() || '';
            const email = row.children[2]?.textContent.toLowerCase() || '';
            const role = row.children[3]?.textContent.toLowerCase() || '';
            const status = row.children[4]?.textContent.toLowerCase() || '';
            const matchSearch = name.includes(searchVal) || email.includes(searchVal);
            const matchRole = !roleVal || role === roleVal;
            const matchStatus = !statusVal || status === statusVal;
            row.style.display = (matchSearch && matchRole && matchStatus) ? '' : 'none';
        });
    }

// Pending Requests
const searchPending=document.getElementById('searchPending');
const roleFilterPending=document.getElementById('roleFilterPending');
if (searchPending && roleFilterPending) {
    [searchPending,roleFilterPending].forEach(el=>el.addEventListener('input',()=>filterTable('pendingAuthorityTable',searchPending,roleFilterPending)));
}


// All Users
const searchAll=document.getElementById('searchAll');
const roleFilterAll=document.getElementById('roleFilterAll');
const statusFilterAll=document.getElementById('statusFilterAll');
if (searchAll && roleFilterAll && statusFilterAll) {
    [searchAll,roleFilterAll,statusFilterAll].forEach(el=>el.addEventListener('input',()=>filterTable('allTable',searchAll,roleFilterAll,statusFilterAll)));
}


    // Confirm buttons
    document.querySelectorAll('button[data-confirm]').forEach(btn => {
        btn.addEventListener('click', e => {
            if (!confirm(btn.getAttribute('data-confirm'))) e.preventDefault();
        });
    });

    function escapeHTML(str) {
        var p = document.createElement("p");
        p.appendChild(document.createTextNode(str));
        return p.innerHTML;
    }
});
