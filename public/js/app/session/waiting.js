setInterval(async () => {
    await get_participants();
}, 2000);



async function get_participants() {
    const sessionId = document.getElementById('session_id').value;
    try {
        const endpoint = route(`session/${sessionId}/participants`);
        const response = await fetch(endpoint);
        const data = await response.json();
        await participants_quantities(data.participants.length);
        await users_fill(data.participants);
    } catch (error) {
        console.error('Error fetching participants:', error);
    }
}

async function users_fill(participants) {
    const userList = document.querySelector('.user-list');
    userList.innerHTML = '';
    participants.forEach(participant => {
        let content = `<div class="user-item selected">
                                    <img src="https://i.pravatar.cc/150?img=12" alt="Luiza Santos" class="user-avatar">
                                    <div class="user-info">
                                        <div class="user-name">${participant.user_name}</div>
                                        <div class="user-email">Entrou há ${getElapsedTime(participant.created_at)}</div>
                                    </div>
                                    <div class="user-action">
                                        <div class="check-icon">
                                            <i class="ph ph-check"></i>
                                        </div>
                                    </div>
                                </div>`;
        userList.innerHTML += content;
    });
}

async function participants_quantities(quantities) {
    const statusText = document.getElementById('statusText');
    statusText.innerHTML = quantities > 1 ?
        `<span>${quantities} participantes conectados</span>` :
        `<span>Aguardando participantes...</span>`;
}

function getElapsedTime(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now - date;
    const diffSecs = Math.floor(diffMs / 1000);
    const diffMins = Math.floor(diffSecs / 60);
    const diffHours = Math.floor(diffMins / 60);
    const diffDays = Math.floor(diffHours / 24);

    if (diffSecs < 60) return `${diffSecs}s`;
    if (diffMins < 60) return `${diffMins}m`;
    if (diffHours < 24) return `${diffHours}h`;
    return `${diffDays}d`;
}