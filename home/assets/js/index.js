// Smooth scroll for arrow
document.addEventListener('click', (e) => {
  const btn = e.target.closest('[data-scroll]');
  if (!btn) return;

  const sel = btn.getAttribute('data-scroll');
  const target = document.querySelector(sel);
  if (!target) return;

  e.preventDefault();
  target.scrollIntoView({ behavior: 'smooth', block: 'start' });
});

// Notices Modal
document.addEventListener('DOMContentLoaded', () => {
    const noticesBtn = document.getElementById('notices-btn');
    const modal = document.getElementById('notices-modal');
    const closeBtn = document.querySelector('.close-btn');
    const noticesContainer = document.getElementById('notices-container');

    const API_URL = '/pms/dashboard/port_director/director_api.php?action=get_notices';

    const fetchNotices = async () => {
        try {
            const response = await fetch(API_URL);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const notices = await response.json();
            renderNotices(notices);
        } catch (error) {
            console.error('Failed to fetch notices:', error);
            noticesContainer.innerHTML = '<p>Could not load notices at this time.</p>';
        }
    };

    const renderNotices = (notices) => {
        noticesContainer.innerHTML = '';
        if (notices && notices.length > 0) {
            notices.forEach(notice => {
                const noticeElement = document.createElement('div');
                noticeElement.classList.add('notice-item');
                noticeElement.innerHTML = `
                    <h3>${notice.title}</h3>
                    <p>${notice.message}</p>
                    <small>${new Date(notice.created_at).toLocaleString()}</small>
                `;
                noticesContainer.appendChild(noticeElement);
            });
        } else {
            noticesContainer.innerHTML = '<p>No notices found.</p>';
        }
    };

    noticesBtn.addEventListener('click', (e) => {
        e.preventDefault();
        modal.style.display = 'block';
        fetchNotices();
    });

    closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});
